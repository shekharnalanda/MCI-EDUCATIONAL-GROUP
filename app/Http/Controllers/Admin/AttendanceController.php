<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceBranch;
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
        $students = $scope->applyInstitutionScope(AttendanceStudent::with(['institution', 'branch', 'irisTemplates']), $request->user())
            ->orderBy('name')->paginate(20, ['*'], 'students_page')->withQueryString();
        $devices = $scope->applyInstitutionScope(AttendanceDevice::with(['institution', 'branch']), $request->user())
            ->orderBy('name')->get();

        $today = $scope->applyInstitutionScope(AttendanceRecord::query(), $request->user())
            ->whereDate('attendance_date', today());

        $institutions = $scope->applyInstitutionScope(
            Institution::query(),
            $request->user()
        )->where('is_active', true)->orderBy('name')->get();

        $branches = $scope->applyInstitutionScope(
            AttendanceBranch::with('institution'),
            $request->user()
        )->orderBy('institution_id')->orderBy('name')->get();

        $openAttendance = $scope->applyInstitutionScope(
            AttendanceRecord::with([
                'institution',
                'branch',
                'student',
                'device',
            ]),
            $request->user()
        )
            ->where('status', 'present')
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->latest('checked_in_at')
            ->limit(50)
            ->get();

        return view('admin.attendance.index', [
            'institutions' => $institutions,
            'branches' => $branches,
            'openAttendance' => $openAttendance,
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
            'attendance_branch_id' => ['nullable','integer','exists:attendance_branches,id'],
            'device_code' => ['nullable','alpha_dash','max:80','unique:attendance_devices,device_code'],
            'serial_number' => ['nullable','string','max:120'],
            'location' => ['nullable','string','max:255'],
        ]);
        if (! empty($data['attendance_branch_id'])) {
            $validBranch = AttendanceBranch::query()
                ->whereKey($data['attendance_branch_id'])
                ->where('institution_id', $data['institution_id'])
                ->exists();

            abort_unless(
                $validBranch,
                422,
                'Device branch does not belong to selected institution.'
            );
        }

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

    private function centralReportQuery(
        Request $request,
        AdminAccessScope $scope
    ) {
        $query = AttendanceRecord::query()
            ->with([
                'institution',
                'branch',
                'student',
                'device',
            ]);

        $query = $scope->applyInstitutionScope(
            $query,
            $request->user()
        );

        if ($request->filled('institution_id')) {
            $query->where(
                'institution_id',
                $request->integer('institution_id')
            );
        }

        if ($request->filled('branch_id')) {
            $query->where(
                'attendance_branch_id',
                $request->integer('branch_id')
            );
        }

        if ($request->filled('person_type')) {
            $type = $request->string('person_type')->toString();

            $query->whereHas(
                'student',
                fn ($q) => $q->where('person_type', $type)
            );
        }

        if ($request->filled('person_id')) {
            $query->where(
                'attendance_student_id',
                $request->integer('person_id')
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString()
            );
        }

        if ($request->filled('checkout_source')) {
            $query->where(
                'checkout_source',
                $request->string('checkout_source')->toString()
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'attendance_date',
                '>=',
                $request->date('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'attendance_date',
                '<=',
                $request->date('date_to')
            );
        }

        return $query;
    }

    public function centralReport(
        Request $request,
        AdminAccessScope $scope
    ) {
        $query = $this->centralReportQuery($request, $scope);

        $summaryQuery = clone $query;

        $summary = [
            'records' => (clone $summaryQuery)->count(),
            'open' => (clone $summaryQuery)
                ->where('status', 'present')
                ->whereNull('checked_out_at')
                ->count(),
            'completed' => (clone $summaryQuery)
                ->where('status', 'completed')
                ->count(),
            'auto_checkout' => (clone $summaryQuery)
                ->where('checkout_source', 'system_auto')
                ->count(),
            'minutes' => (int) (clone $summaryQuery)
                ->sum('minutes_completed'),
        ];

        $records = $query
            ->latest('attendance_date')
            ->latest('checked_in_at')
            ->paginate(100)
            ->withQueryString();

        $institutions = $scope->applyInstitutionScope(
            Institution::query(),
            $request->user()
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $branches = $scope->applyInstitutionScope(
            AttendanceBranch::with('institution'),
            $request->user()
        )
            ->orderBy('institution_id')
            ->orderBy('name')
            ->get();

        $people = $scope->applyInstitutionScope(
            AttendanceStudent::with([
                'institution',
                'branch',
            ]),
            $request->user()
        )
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'admin.attendance.report',
            compact(
                'records',
                'summary',
                'institutions',
                'branches',
                'people'
            )
        );
    }

    public function exportCentralCsv(
        Request $request,
        AdminAccessScope $scope
    ) {
        $records = $this
            ->centralReportQuery($request, $scope)
            ->latest('attendance_date')
            ->latest('checked_in_at')
            ->get();

        $filename =
            'mci-central-attendance-'.
            now()->format('Ymd-His').
            '.csv';

        return response()->streamDownload(
            function () use ($records): void {
                $out = fopen('php://output', 'w');

                fwrite($out, "\xEF\xBB\xBF");

                fputcsv($out, [
                    'Date',
                    'Institution',
                    'Branch',
                    'Person Type',
                    'Name',
                    'Admission/Employee/Roll',
                    'Device',
                    'Mark-In',
                    'Mark-Out',
                    'Mark-Out Source',
                    'Minutes',
                    'Status',
                ]);

                foreach ($records as $record) {
                    $person = $record->student;

                    fputcsv($out, [
                        $record->attendance_date?->format('Y-m-d'),
                        $record->institution?->name,
                        $record->branch?->name
                            ?: 'Main / Unassigned',
                        ucfirst(
                            $person?->person_type ?: 'student'
                        ),
                        $person?->name,
                        $person?->admission_number
                            ?: $person?->employee_number
                            ?: $person?->roll_number,
                        $record->device?->name,
                        $record->checked_in_at?->format(
                            'Y-m-d H:i:s'
                        ),
                        $record->checked_out_at?->format(
                            'Y-m-d H:i:s'
                        ),
                        $record->checkout_source,
                        $record->minutes_completed,
                        $record->status,
                    ]);
                }

                fclose($out);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    public function printCentralReport(
        Request $request,
        AdminAccessScope $scope
    ) {
        $query = $this->centralReportQuery(
            $request,
            $scope
        );

        $summaryQuery = clone $query;

        $summary = [
            'records' => (clone $summaryQuery)->count(),
            'open' => (clone $summaryQuery)
                ->where('status', 'present')
                ->whereNull('checked_out_at')
                ->count(),
            'completed' => (clone $summaryQuery)
                ->where('status', 'completed')
                ->count(),
            'auto_checkout' => (clone $summaryQuery)
                ->where('checkout_source', 'system_auto')
                ->count(),
            'minutes' => (int) (clone $summaryQuery)
                ->sum('minutes_completed'),
        ];

        $records = $query
            ->latest('attendance_date')
            ->latest('checked_in_at')
            ->get();

        return view(
            'admin.attendance.print',
            compact('records', 'summary')
        );
    }

    public function storeBranch(
        Request $request,
        AdminAccessScope $scope,
        AuditLogger $audit
    ) {
        $data = $request->validate([
            'institution_id' => [
                'required',
                'integer',
                'exists:institutions,id',
            ],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'alpha_dash', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $scope->authorizeInstitution(
            $request->user(),
            (int) $data['institution_id']
        );

        $exists = AttendanceBranch::query()
            ->where('institution_id', $data['institution_id'])
            ->where('code', $data['code'])
            ->exists();

        abort_if($exists, 422, 'Branch code already exists.');

        $branch = AttendanceBranch::create([
            'institution_id' => $data['institution_id'],
            'name' => $data['name'],
            'code' => $data['code'],
            'address' => $data['address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $audit->record(
            'attendance.branch.created',
            $branch,
            [],
            $branch->only([
                'institution_id',
                'name',
                'code',
                'is_active',
            ])
        );

        return back()->with(
            'success',
            'Attendance branch created successfully.'
        );
    }

    public function updateBranch(
        Request $request,
        AttendanceBranch $branch,
        AdminAccessScope $scope,
        AuditLogger $audit
    ) {
        $scope->authorizeInstitution(
            $request->user(),
            (int) $branch->institution_id
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'alpha_dash', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $duplicate = AttendanceBranch::query()
            ->where('institution_id', $branch->institution_id)
            ->where('code', $data['code'])
            ->whereKeyNot($branch->id)
            ->exists();

        abort_if($duplicate, 422, 'Branch code already exists.');

        $old = $branch->only([
            'name',
            'code',
            'address',
            'is_active',
        ]);

        $branch->update([
            'name' => $data['name'],
            'code' => $data['code'],
            'address' => $data['address'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $audit->record(
            'attendance.branch.updated',
            $branch,
            $old,
            $branch->only(array_keys($old))
        );

        return back()->with('success', 'Branch updated.');
    }

    private function studentData(Request $request, ?AttendanceStudent $student = null): array
    {
        $data = $request->validate([
            'institution_id' => ['required','exists:institutions,id'],
            'attendance_branch_id' => ['nullable','integer','exists:attendance_branches,id'],
            'person_type' => ['required','in:student,teacher,staff,administrator'],
            'admission_number' => ['nullable','string','max:100','unique:attendance_students,admission_number,'.($student?->id ?? 'NULL').',id,institution_id,'.$request->input('institution_id')],
            'employee_number' => ['nullable','string','max:100'],
            'roll_number' => ['nullable','string','max:100'],
            'name' => ['required','string','max:150'],
            'course_class' => ['nullable','string','max:150'],
            'batch_section' => ['nullable','string','max:100'],
            'photo_path' => ['nullable','string','max:255'],
            'mobile' => ['nullable','string','max:30'],
            'status' => ['required','in:active,inactive,completed'],
        ]);

        if (! empty($data['attendance_branch_id'])) {
            $validBranch = AttendanceBranch::query()
                ->whereKey($data['attendance_branch_id'])
                ->where('institution_id', $data['institution_id'])
                ->exists();

            abort_unless(
                $validBranch,
                422,
                'Selected branch does not belong to this institution.'
            );
        }

        return $data;
    }
}
