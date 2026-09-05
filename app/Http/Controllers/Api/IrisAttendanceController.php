<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDevice;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\IrisTemplate;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IrisAttendanceController extends Controller
{
    public function heartbeat(Request $request): JsonResponse
    {
        $device = $this->authorizedDevice($request);
        if ($device instanceof JsonResponse) return $device;
        $device->update(['last_seen_at' => now(), 'metadata' => array_filter([
            'agent_version' => $request->input('agent_version'),
            'sdk_version' => $request->input('sdk_version'),
            'computer_name' => $request->input('computer_name'),
        ])]);
        return response()->json(['success'=>true, 'server_time'=>now()->toIso8601String(), 'institution'=>$device->institution->only(['id','name','slug'])]);
    }

    public function roster(Request $request): JsonResponse
    {
        $device = $this->authorizedDevice($request);
        if ($device instanceof JsonResponse) return $device;
        $students = AttendanceStudent::where('institution_id', $device->institution_id)
            ->where('status', 'active')->with(['irisTemplates' => fn ($q) => $q->whereNull('revoked_at')])
            ->orderBy('id')->get()->map(fn ($student) => [
                'id' => $student->id,
                'attendance_code' => $student->attendance_code,
                'name' => $student->name,
                'admission_number' => $student->admission_number,
                'roll_number' => $student->roll_number,
                'course_class' => $student->course_class,
                'batch_section' => $student->batch_section,
                'photo_url' => $student->photo_path ? asset($student->photo_path) : null,
                'templates' => $student->irisTemplates->map(fn ($template) => [
                    'eye' => $template->eye,
                    'template' => $template->template_data,
                    'template_hash' => $template->template_hash,
                    'sdk_version' => $template->sdk_version,
                ])->values(),
            ]);
        $device->update(['last_seen_at' => now()]);
        return response()->json(['success'=>true, 'generated_at'=>now()->toIso8601String(), 'students'=>$students]);
    }

    public function enroll(Request $request): JsonResponse
    {
        $device = $this->authorizedDevice($request);
        if ($device instanceof JsonResponse) return $device;
        $data = $request->validate([
            'student_id' => ['required','integer'],
            'eye' => ['required','in:left,right,both,unknown'],
            'template' => ['required','string','max:262144'],
            'quality_score' => ['nullable','numeric','min:0'],
            'sdk_version' => ['nullable','string','max:80'],
        ]);
        $student = AttendanceStudent::whereKey($data['student_id'])->where('institution_id', $device->institution_id)->where('status','active')->first();
        if (!$student) return response()->json(['success'=>false,'message'=>'Student is not available for this institution.'], 404);
        $decoded = base64_decode($data['template'], true);
        if ($decoded === false || strlen($decoded) < 32) return response()->json(['success'=>false,'message'=>'Invalid iris template payload.'], 422);

        $template = IrisTemplate::updateOrCreate(
            ['attendance_student_id'=>$student->id, 'eye'=>$data['eye']],
            ['template_data'=>$data['template'], 'template_hash'=>hash('sha256',$decoded),
             'quality_score'=>$data['quality_score'] ?? null, 'sdk_version'=>$data['sdk_version'] ?? null,
             'enrolled_device_id'=>$device->id, 'enrolled_at'=>now(), 'revoked_at'=>null]
        );
        $device->update(['last_seen_at' => now()]);
        return response()->json(['success'=>true, 'message'=>'Iris enrolled successfully.', 'student'=>$student->only(['id','name','admission_number']), 'eye'=>$template->eye], 201);
    }

    public function mark(Request $request): JsonResponse
    {
        $device = $this->authorizedDevice($request);
        if ($device instanceof JsonResponse) return $device;
        $data = $request->validate([
            'event_uuid' => ['required','uuid'],
            'student_id' => ['required','integer'],
            'captured_at' => ['required','date'],
            'session_key' => ['nullable','alpha_dash','max:40'],
            'match_score' => ['nullable','numeric','min:0'],
            'quality_score' => ['nullable','numeric','min:0'],
            'agent_version' => ['nullable','string','max:80'],
        ]);
        $student = AttendanceStudent::whereKey($data['student_id'])->where('institution_id',$device->institution_id)->where('status','active')->first();
        if (!$student) return response()->json(['success'=>false,'message'=>'Student is not active in this institution.'], 404);
        if (!$student->irisTemplates()->whereNull('revoked_at')->exists()) return response()->json(['success'=>false,'message'=>'Student does not have an active iris enrollment.'], 409);
        $capturedAt = Carbon::parse($data['captured_at']);
        if ($capturedAt->gt(now()->addMinutes(10)) || $capturedAt->lt(now()->subDays(7))) return response()->json(['success'=>false,'message'=>'Capture time is outside the allowed sync window.'], 422);
        $sessionKey = $data['session_key'] ?? 'daily';

        $existingEvent = AttendanceRecord::where('event_uuid',$data['event_uuid'])->first();
        if ($existingEvent) return $this->attendanceResponse($existingEvent, true);

        try {
            [$record, $duplicate] = DB::transaction(function () use ($device,$student,$data,$capturedAt,$sessionKey) {
                $existing = AttendanceRecord::where('institution_id',$device->institution_id)
                    ->where('attendance_student_id',$student->id)->whereDate('attendance_date',$capturedAt->toDateString())
                    ->where('session_key',$sessionKey)->lockForUpdate()->first();
                if ($existing) return [$existing, true];
                return [AttendanceRecord::create([
                    'institution_id'=>$device->institution_id, 'attendance_student_id'=>$student->id,
                    'attendance_device_id'=>$device->id, 'event_uuid'=>$data['event_uuid'],
                    'attendance_date'=>$capturedAt->toDateString(), 'session_key'=>$sessionKey,
                    'captured_at'=>$capturedAt, 'received_at'=>now(), 'method'=>'iris', 'status'=>'present',
                    'match_score'=>$data['match_score'] ?? null, 'quality_score'=>$data['quality_score'] ?? null,
                    'metadata'=>array_filter(['agent_version'=>$data['agent_version'] ?? null]),
                ]), false];
            });
        } catch (QueryException) {
            $record = AttendanceRecord::where('event_uuid',$data['event_uuid'])->first()
                ?? AttendanceRecord::where('institution_id',$device->institution_id)->where('attendance_student_id',$student->id)
                    ->whereDate('attendance_date',$capturedAt->toDateString())->where('session_key',$sessionKey)->firstOrFail();
            $duplicate = true;
        }
        $device->update(['last_seen_at'=>now()]);
        return $this->attendanceResponse($record, $duplicate);
    }

    private function attendanceResponse(AttendanceRecord $record, bool $duplicate): JsonResponse
    {
        $record->loadMissing(['student','institution','device']);
        return response()->json([
            'success'=>true, 'duplicate'=>$duplicate, 'message'=>$duplicate ? 'Attendance already marked.' : 'Attendance marked successfully.',
            'attendance'=>['id'=>$record->id, 'date'=>$record->attendance_date?->format('Y-m-d'),
                'time'=>$record->captured_at?->format('H:i:s'), 'session'=>$record->session_key, 'status'=>$record->status],
            'student'=>['id'=>$record->student->id, 'name'=>$record->student->name,
                'admission_number'=>$record->student->admission_number, 'roll_number'=>$record->student->roll_number,
                'course_class'=>$record->student->course_class, 'batch_section'=>$record->student->batch_section,
                'photo_url'=>$record->student->photo_path ? asset($record->student->photo_path) : null],
            'institution'=>$record->institution->only(['id','name','slug']),
        ], $duplicate ? 200 : 201);
    }

    private function authorizedDevice(Request $request): AttendanceDevice|JsonResponse
    {
        $code = (string) $request->header('X-MCI-Device-Code');
        $token = (string) $request->header('X-MCI-Device-Token');
        $device = AttendanceDevice::with('institution')->where('device_code',$code)->where('is_active',true)->first();
        if (!$device || !$device->institution?->is_active) return response()->json(['success'=>false,'message'=>'Iris device is not active.'],403);
        if (!$token || !$device->token_hash || !hash_equals($device->token_hash,hash('sha256',$token))) {
            return response()->json(['success'=>false,'message'=>'Invalid iris device credentials.'],401);
        }
        return $device;
    }
}
