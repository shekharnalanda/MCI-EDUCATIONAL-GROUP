@extends('admin.layouts.app')
@section('title','Iris Attendance')
@section('content')

<!-- MCI-CENTRAL-BIOMETRIC-V2 -->

<style>
.mci-bio-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:12px
}
.mci-bio-card{
    border:1px solid #dee7f1;
    border-radius:14px;
    background:#fff
}
.mci-bio-stat{
    padding:16px
}
.mci-bio-stat small{
    display:block;
    color:#718096;
    font-weight:700
}
.mci-bio-stat strong{
    display:block;
    margin-top:4px;
    font-size:24px;
    color:#063b76
}
.mci-bio-badge{
    display:inline-block;
    padding:4px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    background:#eaf2ff;
    color:#0755a5
}
.mci-bio-badge.open{
    background:#fff3cd;
    color:#805b00
}
.mci-bio-badge.staff{
    background:#e9f8ef;
    color:#176b39
}
.mci-bio-badge.teacher{
    background:#f1eaff;
    color:#66409a
}
.mci-bio-badge.auto{
    background:#fff0e5;
    color:#9a4c00
}
@media(max-width:900px){
    .mci-bio-grid{
        grid-template-columns:repeat(2,minmax(0,1fr))
    }
}
@media(max-width:600px){
    .mci-bio-grid{
        grid-template-columns:1fr
    }
}
</style>

<div class="alert alert-primary border-0 shadow-sm mb-4">
    <strong>MCI Central Biometric Attendance V2</strong><br>
    Institution → Branch → Student / Teacher / Staff →
    MIS100V2 Iris → Mark-In / Mark-Out → Central Monitoring
</div>

<div class="mci-bio-grid mb-4">

    <div class="mci-bio-card mci-bio-stat">
        <small>INSTITUTIONS</small>
        <strong>{{ $institutions->count() }}</strong>
    </div>

    <div class="mci-bio-card mci-bio-stat">
        <small>BRANCHES</small>
        <strong>{{ $branches->count() }}</strong>
    </div>

    <div class="mci-bio-card mci-bio-stat">
        <small>OPEN ATTENDANCE</small>
        <strong>{{ $openAttendance->count() }}</strong>
    </div>

    <div class="mci-bio-card mci-bio-stat">
        <small>DEVICES ONLINE</small>
        <strong>{{ $onlineDevices }}</strong>
    </div>

</div>

<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Institution Branches</h5>
            <small class="text-muted">
                प्रत्येक campus / branch को उसके institution के अंतर्गत register करें।
            </small>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route('admin.attendance.branches.store') }}"
        class="row g-2 border rounded-3 p-3 mb-4"
    >
        @csrf

        <div class="col-md-3">
            <label class="form-label">Institution</label>
            <select
                class="form-select"
                name="institution_id"
                required
            >
                <option value="">Select Institution</option>

                @foreach($institutions as $institution)
                    <option value="{{ $institution->id }}">
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Branch Code</label>
            <input
                class="form-control"
                name="code"
                placeholder="BSH01"
                required
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Branch Name</label>
            <input
                class="form-control"
                name="name"
                placeholder="Bihar Sharif Campus"
                required
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Address</label>
            <input
                class="form-control"
                name="address"
                placeholder="Branch address"
            >
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <input type="hidden" name="is_active" value="1">
            <button class="btn btn-primary w-100">
                Add
            </button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Institution</th>
                <th>Branch</th>
                <th>Code</th>
                <th>Address</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody>
            @forelse($branches as $branch)
                <tr>
                    <td>{{ $branch->institution?->name }}</td>
                    <td><strong>{{ $branch->name }}</strong></td>
                    <td><code>{{ $branch->code }}</code></td>
                    <td>{{ $branch->address ?: '—' }}</td>
                    <td>
                        <span class="badge {{ $branch->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No attendance branches created yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Live Open Attendance</h5>
            <small class="text-muted">
                Iris Mark-In हो चुका है लेकिन Mark-Out अभी pending है।
                120 मिनट के बाद system स्वतः Mark-Out करेगा।
            </small>
        </div>

        <span class="badge text-bg-warning">
            {{ $openAttendance->count() }} Open
        </span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Person</th>
                <th>Type</th>
                <th>Institution / Branch</th>
                <th>Mark-In</th>
                <th>Device</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody>
            @forelse($openAttendance as $record)
                <tr>
                    <td>
                        <strong>{{ $record->student?->name }}</strong><br>

                        <small class="text-muted">
                            {{ $record->student?->admission_number
                                ?: $record->student?->employee_number
                                ?: $record->student?->roll_number
                                ?: '—' }}
                        </small>
                    </td>

                    <td>
                        @php($type = $record->student?->person_type ?: 'student')

                        <span class="mci-bio-badge {{ $type }}">
                            {{ ucfirst($type) }}
                        </span>
                    </td>

                    <td>
                        {{ $record->institution?->name }}<br>

                        <small class="text-muted">
                            {{ $record->branch?->name ?: 'Main / Unassigned' }}
                        </small>
                    </td>

                    <td>
                        {{ $record->checked_in_at?->format('d M Y') }}<br>

                        <strong>
                            {{ $record->checked_in_at?->format('h:i A') }}
                        </strong>
                    </td>

                    <td>
                        {{ $record->device?->name ?: '—' }}
                    </td>

                    <td>
                        <span class="mci-bio-badge open">
                            Awaiting Mark-Out
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No open biometric attendance right now.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>


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
<div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Students / Teachers / Staff & Iris Enrollment</h5><small class="text-muted">Create the person here, then capture iris from the MCI Biometric Connector.</small></div></div>
<form method="POST" action="{{ route('admin.attendance.students.store') }}" class="border rounded-3 p-3 mb-4">@csrf

<div class="row g-2 mb-3">

    <div class="col-md-3">
        <label class="form-label">Person Type</label>

        <select
            class="form-select"
            name="person_type"
            required
        >
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="staff">Staff</option>
            <option value="administrator">Administrator</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Branch</label>

        <select
            class="form-select"
            name="attendance_branch_id"
        >
            <option value="">Main / Unassigned</option>

            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">
                    {{ $branch->institution?->name }}
                    — {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Employee Number</label>

        <input
            class="form-control"
            name="employee_number"
            placeholder="For Teacher / Staff"
        >
    </div>

</div>

<div class="row g-2">
@if($institutions->isNotEmpty())<div class="col-md-3"><select name="institution_id" class="form-select" required><option value="">Select institution</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}">{{ $institution->name }}</option>@endforeach</select></div>@else<input type="hidden" name="institution_id" value="{{ auth()->user()->institution_id }}">@endif
<div class="col-md-3"><input name="name" class="form-control" placeholder="Student name" required></div><div class="col-md-2"><input name="admission_number" class="form-control" placeholder="Admission no."></div><div class="col-md-2"><input name="roll_number" class="form-control" placeholder="Roll no."></div><div class="col-md-2"><input name="mobile" class="form-control" placeholder="Mobile"></div>
<div class="col-md-3"><input name="course_class" class="form-control" placeholder="Course / class"></div><div class="col-md-3"><input name="batch_section" class="form-control" placeholder="Batch / section"></div><div class="col-md-4"><input name="photo_path" class="form-control" placeholder="Photo path (optional)"></div><input type="hidden" name="status" value="active"><div class="col-md-2"><button class="btn btn-primary w-100">Add Student</button></div>
</div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Person</th><th>Type</th><th>Institution / Branch</th><th>Course / Batch</th><th>Iris</th><th>Status</th></tr></thead><tbody>
@forelse($students as $student)<tr><td>
<strong>{{ $student->name }}</strong><br>
<small class="text-muted">
{{ $student->admission_number
    ?: $student->employee_number
    ?: $student->roll_number
    ?: '—' }}
</small>
</td>
<td>
<span class="mci-bio-badge {{ $student->person_type }}">
{{ ucfirst($student->person_type ?: 'student') }}
</span>
</td>
<td>
{{ $student->institution?->name }}<br>
<small class="text-muted">
{{ $student->branch?->name ?: 'Main / Unassigned' }}
</small>
</td><td>{{ $student->course_class ?: '—' }}<br><small>{{ $student->batch_section }}</small></td><td>@if($student->irisTemplates->whereNull('revoked_at')->count())<span class="badge text-bg-success">Enrolled ({{ $student->irisTemplates->whereNull('revoked_at')->count() }})</span>@else<span class="badge text-bg-warning">Pending</span>@endif</td><td>{{ ucfirst($student->status) }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No attendance persons created yet.</td></tr>@endforelse
</tbody></table></div>{{ $students->links() }}
</div>

@if(auth()->user()->isMasterAdmin())
<div class="card p-4">
<h5 class="fw-bold mb-1">MIS100V2 Devices</h5><p class="text-muted">Register each laptop/desktop device against its institution.</p>
<form method="POST" action="{{ route('admin.attendance.devices.store') }}" class="border rounded-3 p-3 mb-4">@csrf

<div class="row g-2 mb-3">
    <div class="col-md-5">
        <label class="form-label">Device Branch</label>

        <select
            class="form-select"
            name="attendance_branch_id"
        >
            <option value="">Main / Unassigned</option>

            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">
                    {{ $branch->institution?->name }}
                    — {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row g-2"><div class="col-md-3"><select name="institution_id" class="form-select" required><option value="">Select institution</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}">{{ $institution->name }}</option>@endforeach</select></div><div class="col-md-3"><input name="name" class="form-control" placeholder="Device name" required></div><div class="col-md-2"><input name="device_code" class="form-control" placeholder="Device code (optional)"></div><div class="col-md-2"><input name="serial_number" class="form-control" placeholder="Serial number"></div><div class="col-md-2"><input name="location" class="form-control" placeholder="Location"></div><div class="col-12"><button class="btn btn-dark">Register Device & Generate Token</button></div></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Device</th><th>Institution</th><th>Code</th><th>Last Seen</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($devices as $device)<tr><td><strong>{{ $device->name }}</strong><br>
<small class="text-muted">
{{ $device->branch?->name ?: 'Main / Unassigned' }}
@if($device->location)
 • {{ $device->location }}
@endif
</small></td><td>{{ $device->institution?->name }}</td><td><code>{{ $device->device_code }}</code></td><td>{{ $device->last_seen_at?->diffForHumans() ?: 'Never' }}</td><td><span class="badge {{ $device->is_active?'text-bg-success':'text-bg-secondary' }}">{{ $device->is_active?'Active':'Disabled' }}</span></td><td><div class="d-flex gap-2"><form method="POST" action="{{ route('admin.attendance.devices.token',$device) }}">@csrf<button class="btn btn-sm btn-outline-dark" onclick="return confirm('Regenerate token? The old token will stop working.')">New Token</button></form><form method="POST" action="{{ route('admin.attendance.devices.toggle',$device) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-{{ $device->is_active?'danger':'success' }}">{{ $device->is_active?'Disable':'Enable' }}</button></form></div></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No iris devices registered.</td></tr>@endforelse
</tbody></table></div>
</div>
@endif
@endsection
