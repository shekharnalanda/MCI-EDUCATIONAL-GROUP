<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>MCI Central Attendance Report</title>

<style>
@page{size:A4 landscape;margin:10mm}
*{box-sizing:border-box}
body{
    margin:0;
    font-family:Arial,sans-serif;
    color:#172b4d;
    font-size:10px
}
.header{
    display:flex;
    justify-content:space-between;
    border-bottom:3px solid #0755a5;
    padding-bottom:10px
}
.header h1{
    margin:0;
    color:#062b63;
    font-size:20px
}
.header h2{
    margin:4px 0;
    font-size:14px
}
.meta{
    text-align:right;
    color:#607086
}
.summary{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:6px;
    margin:12px 0
}
.box{
    border:1px solid #d4deea;
    padding:8px;
    border-radius:5px
}
.box small{
    display:block;
    color:#718096;
    font-size:7px
}
.box strong{
    font-size:14px;
    color:#062b63
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px
}
th,td{
    border:1px solid #ccd9e8;
    padding:5px;
    text-align:left;
    vertical-align:top
}
th{
    background:#eef4fb;
    font-size:7px;
    text-transform:uppercase
}
.no-print{
    margin-bottom:10px;
    padding:8px 14px;
    border:0;
    border-radius:6px;
    background:#0755a5;
    color:#fff;
    font-weight:bold;
    cursor:pointer
}
.footer{
    display:flex;
    justify-content:space-between;
    margin-top:16px;
    color:#718096
}
@media print{
    .no-print{display:none}
}
</style>
</head>

<body>

<button class="no-print" onclick="window.print()">
Print / Save as PDF
</button>

<div class="header">

<div>
<h1>MCI EDUCATIONAL GROUP</h1>
<h2>Central Biometric Attendance Report</h2>
<div>
Institution / Branch / Student / Teacher / Staff
</div>
</div>

<div class="meta">
Generated:
{{ now()->format('d-m-Y h:i A') }}<br>
MIS100V2 Central Attendance Management
</div>

</div>

<div class="summary">

<div class="box">
<small>RECORDS</small>
<strong>{{ number_format($summary['records']) }}</strong>
</div>

<div class="box">
<small>OPEN</small>
<strong>{{ number_format($summary['open']) }}</strong>
</div>

<div class="box">
<small>COMPLETED</small>
<strong>{{ number_format($summary['completed']) }}</strong>
</div>

<div class="box">
<small>AUTO MARK-OUT</small>
<strong>{{ number_format($summary['auto_checkout']) }}</strong>
</div>

<div class="box">
<small>TOTAL MINUTES</small>
<strong>{{ number_format($summary['minutes']) }}</strong>
</div>

</div>

<table>

<thead>
<tr>
<th>Date</th>
<th>Person</th>
<th>Type</th>
<th>Institution</th>
<th>Branch</th>
<th>Device</th>
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
{{ $record->attendance_date?->format('d-m-Y') ?: '—' }}
</td>

<td>
<strong>{{ $record->student?->name ?: '—' }}</strong><br>
{{ $record->student?->admission_number
    ?: $record->student?->employee_number
    ?: $record->student?->roll_number
    ?: '—' }}
</td>

<td>
{{ ucfirst($record->student?->person_type ?: 'student') }}
</td>

<td>
{{ $record->institution?->name ?: '—' }}
</td>

<td>
{{ $record->branch?->name ?: 'Main / Unassigned' }}
</td>

<td>
{{ $record->device?->name ?: '—' }}
</td>

<td>
{{ $record->checked_in_at?->format('d-m-Y h:i A') ?: '—' }}
</td>

<td>
{{ $record->checked_out_at?->format('d-m-Y h:i A') ?: '—' }}
</td>

<td>
@if($record->checkout_source === 'system_auto')
System Auto Mark-Out
@elseif($record->checkout_source === 'iris')
Iris Mark-Out
@else
—
@endif
</td>

<td>
{{ (int)$record->minutes_completed }}
</td>

<td>
{{ ucfirst((string)$record->status) }}
</td>

</tr>

@empty

<tr>
<td colspan="11">
No attendance records found for selected filters.
</td>
</tr>

@endforelse

</tbody>
</table>

<div class="footer">
<div>MCI Central Biometric Attendance Management System</div>
<div>Authorized Central Attendance Report</div>
</div>

</body>
</html>
