<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDevice;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\Institution;
use App\Services\AdminAccessScope;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Request $request, AdminAccessScope $scope)
    {
        [$from, $to] = $this->dateRange($request);
        $records = $this->filteredRecords($request, $scope, $from, $to);
        $students = $scope->applyInstitutionScope(AttendanceStudent::with(['institution', 'irisTemplates']), $request->user())
            ->orderBy('name')->paginate(20, ['*'], 'students_page')->withQueryString();
        $devices = $scope->applyInstitutionScope(AttendanceDevice::with('institution'), $request->user())
            ->orderBy('name')->get();

        $today = $scope->applyInstitutionScope(AttendanceRecord::query(), $request->user())
            ->whereDate('attendance_date', today());

        return view('admin.attendance.index', [
            'records' => $records->latest('captured_at')->paginate(30)->withQueryString(),
            'students' => $students,
            'devices' => $devices,
            'institutions' => $request->user()->isMasterAdmin() || $request->user()->role === 'central_manager'
                ? Institution::where('is_active', true)->orderBy('name')->get() : collect(),
            'from' => $from,
            'to' => $to,
            'todayPresent' => (clone $today)->where('status', 'present')->distinct('attendance_student_id')->count('attendance_student_id'),
            'activeStudents' => $scope->applyInstitutionScope(AttendanceStudent::query(), $request->user())->where('status', 'active')->count(),
            'enrolledStudents' => $scope->applyInstitutionScope(AttendanceStudent::whereHas('irisTemplates', fn ($q) => $q->whereNull('revoked_at')), $request->user())->count(),
            'onlineDevices' => $scope->applyInstitutionScope(AttendanceDevice::query(), $request->user())->where('is_active', true)->where('last_seen_at', '>=', now()->subMinutes(5))->count(),
        ]);
    }

    public function storeStudent(Request $request, AdminAccessScope $scope, AuditLogger $audit)
    {
        $data = $this->studentData($request);
        abort_unless($scope->canAccessInstitution($request->user(), (int) $data['institution_id']), 403);
        $student = AttendanceStudent::create($data + ['attendance_code' => (string) Str::uuid()]);
        $audit->record('attendance.student.created', $student, [], $student->only(['institution_id','name','admission_number','course_class','status']));
        return back()->with('success', 'Attendance student added. Iris enrollment can now be completed from the MIS100V2 agent.');
    }

    public function updateStudent(Request $request, AttendanceStudent $student, AdminAccessScope $scope, AuditLogger $audit)
    {
        abort_unless($scope->canAccessInstitution($request->user(), $student->institution_id), 403);
        $data = $this->studentData($request, $student);
        abort_unless($scope->canAccessInstitution($request->user(), (int) $data['institution_id']), 403);
        $old = $student->only(['institution_id','name','admission_number','roll_number','course_class','batch_section','status']);
        $student->update($data);
        $audit->record('attendance.student.updated', $student, $old, $student->only(array_keys($old)));
        return back()->with('success', 'Attendance student updated.');
    }

    public function storeDevice(Request $request, AuditLogger $audit)
    {
        $data = $request->validate([
            'institution_id' => ['required','exists:institutions,id'],
            'name' => ['required','string','max:150'],
            'device_code' => ['nullable','alpha_dash','max:80','unique:attendance_devices,device_code'],
            'serial_number' => ['nullable','string','max:120'],
            'location' => ['nullable','string','max:255'],
        ]);
        $plainToken = 'mci_iris_'.Str::random(48);
        $device = AttendanceDevice::create($data + [
            'device_code' => $data['device_code'] ?: 'iris-'.Str::lower(Str::random(12)),
            'token_hash' => hash('sha256', $plainToken),
            'is_active' => true,
        ]);
        $audit->record('attendance.device.created', $device, [], $device->only(['institution_id','name','device_code','serial_number','location']));
        return back()->with('success', 'Iris device registered. Copy the token now; it will not be shown again.')
            ->with('generated_device_token', $plainToken)
            ->with('generated_device_code', $device->device_code);
    }

    public function rotateDeviceToken(AttendanceDevice $device, AuditLogger $audit)
    {
        $plainToken = 'mci_iris_'.Str::random(48);
        $device->update(['token_hash' => hash('sha256', $plainToken), 'is_active' => true]);
        $audit->record('attendance.device.token_rotated', $device);
        return back()->with('success', 'Device token regenerated. The previous token is no longer valid.')
            ->with('generated_device_token', $plainToken)
            ->with('generated_device_code', $device->device_code);
    }

    public function toggleDevice(AttendanceDevice $device, AuditLogger $audit)
    {
        $old = $device->is_active;
        $device->update(['is_active' => !$old]);
        $audit->record('attendance.device.status_changed', $device, ['is_active'=>$old], ['is_active'=>$device->is_active]);
        return back()->with('success', 'Device status updated.');
    }

    public function export(Request $request, AdminAccessScope $scope)
    {
        [$from, $to] = $this->dateRange($request);
        $rows = $this->filteredRecords($request, $scope, $from, $to)->oldest('captured_at')->get();
        $filename = 'mci-attendance-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Date','Time','Institution','Student','Admission No.','Roll No.','Course/Class','Batch/Section','Session','Status','Device','Method','Match Score']);
            foreach ($rows as $record) {
                fputcsv($out, [
                    $record->attendance_date?->format('Y-m-d'), $record->captured_at?->format('H:i:s'),
                    $record->institution?->name, $record->student?->name, $record->student?->admission_number,
                    $record->student?->roll_number, $record->student?->course_class, $record->student?->batch_section,
                    $record->session_key, $record->status, $record->device?->name, $record->method, $record->match_score,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function print(Request $request, AdminAccessScope $scope)
    {
        [$from, $to] = $this->dateRange($request);
        return view('admin.attendance.print', [
            'records' => $this->filteredRecords($request, $scope, $from, $to)->oldest('captured_at')->get(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    private function filteredRecords(Request $request, AdminAccessScope $scope, Carbon $from, Carbon $to): Builder
    {
        return $scope->applyInstitutionScope(
            AttendanceRecord::with(['institution','student','device']), $request->user()
        )->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->when($request->filled('institution_id'), fn ($q) => $q->where('institution_id', $request->integer('institution_id')))
            ->when($request->filled('student_id'), fn ($q) => $q->where('attendance_student_id', $request->integer('student_id')))
            ->when($request->filled('session_key'), fn ($q) => $q->where('session_key', $request->string('session_key')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.str_replace(['%','_'], ['\\%','\\_'], trim((string) $request->string('q'))).'%';
                $query->whereHas('student', fn ($q) => $q->where('name','like',$term)->orWhere('admission_number','like',$term)->orWhere('roll_number','like',$term));
            });
    }

    private function dateRange(Request $request): array
    {
        $request->validate([
            'from' => ['nullable','date_format:Y-m-d'],
            'to' => ['nullable','date_format:Y-m-d'],
            'institution_id' => ['nullable','integer','exists:institutions,id'],
            'student_id' => ['nullable','integer','exists:attendance_students,id'],
            'session_key' => ['nullable','alpha_dash','max:40'],
            'q' => ['nullable','string','max:150'],
        ]);
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        abort_if($from->gt($to) || $from->diffInDays($to) > 366, 422, 'Invalid attendance date range.');
        return [$from, $to];
    }

    private function studentData(Request $request, ?AttendanceStudent $student = null): array
    {
        return $request->validate([
            'institution_id' => ['required','exists:institutions,id'],
            'admission_number' => ['nullable','string','max:100','unique:attendance_students,admission_number,'.($student?->id ?? 'NULL').',id,institution_id,'.$request->input('institution_id')],
            'roll_number' => ['nullable','string','max:100'],
            'name' => ['required','string','max:150'],
            'course_class' => ['nullable','string','max:150'],
            'batch_section' => ['nullable','string','max:100'],
            'photo_path' => ['nullable','string','max:255'],
            'mobile' => ['nullable','string','max:30'],
            'status' => ['required','in:active,inactive,completed'],
        ]);
    }
}
