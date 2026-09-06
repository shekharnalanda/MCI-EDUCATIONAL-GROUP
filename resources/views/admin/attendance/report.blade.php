@extends('layouts.admin')

@section('title', 'Central Attendance Report')

@section('content')

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-1">
            Central Attendance Reporting
        </h3>
        <div class="text-muted">
            Institution, Branch, Student, Teacher और Staff
            biometric attendance monitoring.
        </div>
    </div>

    <a
        href="{{ route('admin.attendance.index') }}"
        class="btn btn-outline-primary"
    >
        Attendance Dashboard
    </a>
</div>

<div class="row g-3 mb-4">

<div class="col-md">
<div class="card p-3 h-100">
<small class="text-muted fw-bold">RECORDS</small>
<strong class="fs-4">{{ number_format($summary['records']) }}</strong>
</div>
</div>

<div class="col-md">
<div class="card p-3 h-100">
<small class="text-muted fw-bold">OPEN</small>
<strong class="fs-4">{{ number_format($summary['open']) }}</strong>
</div>
</div>

<div class="col-md">
<div class="card p-3 h-100">
<small class="text-muted fw-bold">COMPLETED</small>
<strong class="fs-4">{{ number_format($summary['completed']) }}</strong>
</div>
</div>

<div class="col-md">
<div class="card p-3 h-100">
<small class="text-muted fw-bold">AUTO MARK-OUT</small>
<strong class="fs-4">{{ number_format($summary['auto_checkout']) }}</strong>
</div>
</div>

<div class="col-md">
<div class="card p-3 h-100">
<small class="text-muted fw-bold">TOTAL MINUTES</small>
<strong class="fs-4">{{ number_format($summary['minutes']) }}</strong>
</div>
</div>

</div>

<div class="card p-4 mb-4">

<h5 class="fw-bold mb-3">Report Filters</h5>

<form method="GET" class="row g-2">

<div class="col-md-3">
<label class="form-label">Institution</label>
<select name="institution_id" class="form-select">
<option value="">All Institutions</option>
@foreach($institutions as $institution)
<option
value="{{ $institution->id }}"
@selected((string)request('institution_id') === (string)$institution->id)
>
{{ $institution->name }}
</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<label class="form-label">Branch</label>
<select name="branch_id" class="form-select">
<option value="">All Branches</option>
@foreach($branches as $branch)
<option
value="{{ $branch->id }}"
@selected((string)request('branch_id') === (string)$branch->id)
>
{{ $branch->institution?->name }} — {{ $branch->name }}
</option>
@endforeach
</select>
</div>

<div class="col-md-2">
<label class="form-label">Person Type</label>
<select name="person_type" class="form-select">
<option value="">All</option>
<option value="student" @selected(request('person_type')==='student')>Student</option>
<option value="teacher" @selected(request('person_type')==='teacher')>Teacher</option>
<option value="staff" @selected(request('person_type')==='staff')>Staff</option>
<option value="administrator" @selected(request('person_type')==='administrator')>Administrator</option>
</select>
</div>

<div class="col-md-4">
<label class="form-label">Person</label>
<select name="person_id" class="form-select">
<option value="">All Persons</option>
@foreach($people as $person)
<option
value="{{ $person->id }}"
@selected((string)request('person_id') === (string)$person->id)
>
{{ $person->name }} — {{ ucfirst($person->person_type ?: 'student') }}
</option>
@endforeach
</select>
</div>

<div class="col-md-2">
<label class="form-label">Status</label>
<select name="status" class="form-select">
<option value="">All</option>
<option value="present" @selected(request('status')==='present')>Open / Present</option>
<option value="completed" @selected(request('status')==='completed')>Completed</option>
</select>
</div>

<div class="col-md-2">
<label class="form-label">Mark-Out</label>
<select name="checkout_source" class="form-select">
<option value="">All</option>
<option value="iris" @selected(request('checkout_source')==='iris')>Iris</option>
<option value="system_auto" @selected(request('checkout_source')==='system_auto')>System Auto</option>
</select>
</div>

<div class="col-md-2">
<label class="form-label">From</label>
<input
type="date"
name="date_from"
class="form-control"
value="{{ request('date_from') }}"
>
</div>

<div class="col-md-2">
<label class="form-label">To</label>
<input
type="date"
name="date_to"
class="form-control"
value="{{ request('date_to') }}"
>
</div>

<div class="col-md-4 d-flex align-items-end gap-2">
<button class="btn btn-primary">
Apply Filters
</button>

<a
href="{{ route('admin.attendance.report') }}"
class="btn btn-outline-secondary"
>
Reset
</a>
</div>

</form>

<div class="d-flex flex-wrap gap-2 mt-3">

<a
href="{{ route('admin.attendance.report.csv', request()->query()) }}"
class="btn btn-success"
>
Export Excel / CSV
</a>

<a
href="{{ route('admin.attendance.report.print', request()->query()) }}"
target="_blank"
class="btn btn-dark"
>
Print / Save PDF
</a>

</div>
</div>

<div class="card p-3">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>
<tr>
<th>Date</th>
<th>Person</th>
<th>Institution / Branch</th>
<th>Mark-In</th>
<th>Mark-Out</th>
<th>Exit</th>
<th>Minutes</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@forelse($records as $record)

<tr>

<td>
{{ $record->attendance_date?->format('d-m-Y') }}
</td>

<td>
<strong>{{ $record->student?->name }}</strong><br>
<small class="text-muted">
{{ ucfirst($record->student?->person_type ?: 'student') }}
</small>
</td>

<td>
{{ $record->institution?->name }}<br>
<small class="text-muted">
{{ $record->branch?->name ?: 'Main / Unassigned' }}
</small>
</td>

<td>
{{ $record->checked_in_at?->format('h:i A') ?: '—' }}
</td>

<td>
{{ $record->checked_out_at?->format('h:i A') ?: '—' }}
</td>

<td>
@if($record->checkout_source === 'system_auto')
<span class="badge text-bg-warning">
System Auto
</span>
@elseif($record->checkout_source === 'iris')
<span class="badge text-bg-primary">
Iris
</span>
@else
—
@endif
</td>

<td>
{{ (int)$record->minutes_completed }}
</td>

<td>
@if($record->status === 'completed')
<span class="badge text-bg-success">Completed</span>
@else
<span class="badge text-bg-warning">Open</span>
@endif
</td>

</tr>

@empty

<tr>
<td colspan="8" class="text-center text-muted py-5">
No attendance records found.
</td>
</tr>

@endforelse

</tbody>
</table>

</div>

{{ $records->links() }}

</div>

</div>

@endsection
