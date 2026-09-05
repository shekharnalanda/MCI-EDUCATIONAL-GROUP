<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>MCI Attendance Report</title>
<style>body{font-family:Arial,sans-serif;color:#172033;margin:28px}header{display:flex;align-items:center;gap:16px;border-bottom:3px solid #0b4da2;padding-bottom:14px;margin-bottom:18px}header img{width:78px;height:78px;object-fit:contain}h1{margin:0;font-size:24px}p{margin:5px 0;color:#536072}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #cbd3df;padding:7px;text-align:left}th{background:#eaf1fb}.actions{margin-bottom:18px}.actions button{padding:9px 16px}@media print{.actions{display:none}body{margin:8mm}@page{size:A4 landscape;margin:8mm}}</style>
</head><body>
<div class="actions"><button onclick="window.print()">Print / Save as PDF</button></div>
<header><img src="{{ asset('images/mci-logo.png') }}" alt="MCI logo"><div><h1>MCI Educational Group</h1><p>Central Iris Attendance Report</p><p>{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p></div></header>
<table><thead><tr><th>#</th><th>Date</th><th>Time</th><th>Institution</th><th>Student</th><th>Admission/Roll</th><th>Course/Class</th><th>Batch</th><th>Session</th><th>Status</th><th>Device</th></tr></thead><tbody>
@forelse($records as $record)<tr><td>{{ $loop->iteration }}</td><td>{{ $record->attendance_date?->format('d-m-Y') }}</td><td>{{ $record->captured_at?->format('h:i:s A') }}</td><td>{{ $record->institution?->name }}</td><td>{{ $record->student?->name }}</td><td>{{ $record->student?->admission_number ?: $record->student?->roll_number }}</td><td>{{ $record->student?->course_class }}</td><td>{{ $record->student?->batch_section }}</td><td>{{ $record->session_key }}</td><td>{{ ucfirst($record->status) }}</td><td>{{ $record->device?->name }}</td></tr>@empty<tr><td colspan="11" style="text-align:center">No attendance records found.</td></tr>@endforelse
</tbody></table></body></html>
