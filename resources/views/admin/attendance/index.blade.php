@extends('admin.layouts.app')
@section('title','Iris Attendance')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h2 class="fw-bold mb-1">Central Iris Attendance</h2><p class="text-muted mb-0">MIS100V2 attendance, institution-wise monitoring and student enrollment.</p></div>
    <div class="d-flex gap-2"><a class="btn btn-outline-dark" target="_blank" href="{{ route('admin.attendance.print', request()->query()) }}">Print / Save PDF</a><a class="btn btn-success" href="{{ route('admin.attendance.export', request()->query()) }}">Export Excel-compatible CSV</a></div>
</div>

@if(session('generated_device_token'))
<div class="alert alert-warning">
    <strong>Copy these device credentials now.</strong> The token will not be shown again.<br>
    Device code: <code class="user-select-all">{{ session('generated_device_code') }}</code><br>
    Device token: <code class="user-select-all">{{ session('generated_device_token') }}</code>
</div>
@endif

<div class="row g-3 mb-4">
@foreach([['Today Present',$todayPresent,'primary'],['Active Students',$activeStudents,'dark'],['Iris Enrolled',$enrolledStudents,'success'],['Devices Online',$onlineDevices,'info']] as [$label,$value,$color])
<div class="col-6 col-xl-3"><div class="card p-3 h-100"><small class="text-muted text-uppercase fw-semibold">{{ $label }}</small><div class="display-6 fw-bold text-{{ $color }} mt-2">{{ $value }}</div></div></div>
@endforeach
</div>

<form class="card p-3 mb-4" method="GET">
<div class="row g-2 align-items-end">
    <div class="col-md-2"><label class="form-label small text-muted">From</label><input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control"></div>
    <div class="col-md-2"><label class="form-label small text-muted">To</label><input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control"></div>
    @if($institutions->isNotEmpty())<div class="col-md-3"><label class="form-label small text-muted">Institution</label><select name="institution_id" class="form-select"><option value="">All institutions</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}" @selected((string)request('institution_id')===(string)$institution->id)>{{ $institution->name }}</option>@endforeach</select></div>@endif
    <div class="col-md-3"><label class="form-label small text-muted">Student / Admission / Roll</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Apply Filters</button></div>
</div>
</form>

<div class="card p-4 mb-4">
<h5 class="fw-bold mb-3">Attendance Records</h5>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date & Time</th><th>Student</th><th>Institution</th><th>Course / Batch</th><th>Session</th><th>Device</th><th>Status</th></tr></thead><tbody>
@forelse($records as $record)<tr>
<td><strong>{{ $record->attendance_date?->format('d M Y') }}</strong><br><small class="text-muted">{{ $record->captured_at?->format('h:i:s A') }}</small></td>
<td><strong>{{ $record->student?->name }}</strong><br><small class="text-muted">{{ $record->student?->admission_number ?: $record->student?->roll_number ?: 'No reference' }}</small></td>
<td>{{ $record->institution?->name }}</td><td>{{ $record->student?->course_class ?: '—' }}<br><small class="text-muted">{{ $record->student?->batch_section }}</small></td>
<td><code>{{ $record->session_key }}</code></td><td>{{ $record->device?->name ?: 'Unknown' }}</td><td><span class="badge text-bg-success">{{ ucfirst($record->status) }}</span></td>
</tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">No attendance records found for this period.</td></tr>@endforelse
</tbody></table></div>{{ $records->links() }}
</div>

<div class="card p-4 mb-4">
<div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Students & Iris Enrollment</h5><small class="text-muted">Create the student here, then capture iris from the Windows agent.</small></div></div>
<form method="POST" action="{{ route('admin.attendance.students.store') }}" class="border rounded-3 p-3 mb-4">@csrf
<div class="row g-2">
@if($institutions->isNotEmpty())<div class="col-md-3"><select name="institution_id" class="form-select" required><option value="">Select institution</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}">{{ $institution->name }}</option>@endforeach</select></div>@else<input type="hidden" name="institution_id" value="{{ auth()->user()->institution_id }}">@endif
<div class="col-md-3"><input name="name" class="form-control" placeholder="Student name" required></div><div class="col-md-2"><input name="admission_number" class="form-control" placeholder="Admission no."></div><div class="col-md-2"><input name="roll_number" class="form-control" placeholder="Roll no."></div><div class="col-md-2"><input name="mobile" class="form-control" placeholder="Mobile"></div>
<div class="col-md-3"><input name="course_class" class="form-control" placeholder="Course / class"></div><div class="col-md-3"><input name="batch_section" class="form-control" placeholder="Batch / section"></div><div class="col-md-4"><input name="photo_path" class="form-control" placeholder="Photo path (optional)"></div><input type="hidden" name="status" value="active"><div class="col-md-2"><button class="btn btn-primary w-100">Add Student</button></div>
</div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Student</th><th>Institution</th><th>Course / Batch</th><th>Iris</th><th>Status</th></tr></thead><tbody>
@forelse($students as $student)<tr><td><strong>{{ $student->name }}</strong><br><small class="text-muted">Admission: {{ $student->admission_number ?: '—' }} | Roll: {{ $student->roll_number ?: '—' }}</small></td><td>{{ $student->institution?->name }}</td><td>{{ $student->course_class ?: '—' }}<br><small>{{ $student->batch_section }}</small></td><td>@if($student->irisTemplates->whereNull('revoked_at')->count())<span class="badge text-bg-success">Enrolled ({{ $student->irisTemplates->whereNull('revoked_at')->count() }})</span>@else<span class="badge text-bg-warning">Pending</span>@endif</td><td>{{ ucfirst($student->status) }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No students created yet.</td></tr>@endforelse
</tbody></table></div>{{ $students->links() }}
</div>

@if(auth()->user()->isMasterAdmin())
<div class="card p-4">
<h5 class="fw-bold mb-1">MIS100V2 Devices</h5><p class="text-muted">Register each laptop/desktop device against its institution.</p>
<form method="POST" action="{{ route('admin.attendance.devices.store') }}" class="border rounded-3 p-3 mb-4">@csrf
<div class="row g-2"><div class="col-md-3"><select name="institution_id" class="form-select" required><option value="">Select institution</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}">{{ $institution->name }}</option>@endforeach</select></div><div class="col-md-3"><input name="name" class="form-control" placeholder="Device name" required></div><div class="col-md-2"><input name="device_code" class="form-control" placeholder="Device code (optional)"></div><div class="col-md-2"><input name="serial_number" class="form-control" placeholder="Serial number"></div><div class="col-md-2"><input name="location" class="form-control" placeholder="Location"></div><div class="col-12"><button class="btn btn-dark">Register Device & Generate Token</button></div></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Device</th><th>Institution</th><th>Code</th><th>Last Seen</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($devices as $device)<tr><td><strong>{{ $device->name }}</strong><br><small class="text-muted">{{ $device->location }}</small></td><td>{{ $device->institution?->name }}</td><td><code>{{ $device->device_code }}</code></td><td>{{ $device->last_seen_at?->diffForHumans() ?: 'Never' }}</td><td><span class="badge {{ $device->is_active?'text-bg-success':'text-bg-secondary' }}">{{ $device->is_active?'Active':'Disabled' }}</span></td><td><div class="d-flex gap-2"><form method="POST" action="{{ route('admin.attendance.devices.token',$device) }}">@csrf<button class="btn btn-sm btn-outline-dark" onclick="return confirm('Regenerate token? The old token will stop working.')">New Token</button></form><form method="POST" action="{{ route('admin.attendance.devices.toggle',$device) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-{{ $device->is_active?'danger':'success' }}">{{ $device->is_active?'Disable':'Enable' }}</button></form></div></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No iris devices registered.</td></tr>@endforelse
</tbody></table></div>
</div>
@endif
@endsection
