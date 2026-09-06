@extends('admin.layouts.app')

@section('title','Biometric Devices')

@section('content')

<style>
.bio-head{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:22px}
.bio-head h1{margin:0;color:#17345b}
.bio-sub{color:#64748b}
.bio-card{background:#fff;border:1px solid #dce5ef;border-radius:14px;padding:18px;box-shadow:0 5px 18px rgba(30,60,90,.06)}
.bio-table{width:100%;border-collapse:collapse}
.bio-table th,.bio-table td{padding:12px 10px;border-bottom:1px solid #e7edf4;text-align:left;vertical-align:top}
.bio-table th{font-size:12px;color:#64748b;text-transform:uppercase}
.bio-ok{color:#138a63;font-weight:700}
.bio-off{color:#b42318;font-weight:700}
.bio-btn{border:0;border-radius:8px;padding:8px 11px;font-weight:700;cursor:pointer}
.bio-token{background:#0755a5;color:#fff}
.bio-toggle{background:#edf2f7;color:#17345b}
.bio-secret{background:#fff8e6;border:1px solid #f1d18a;padding:16px;border-radius:10px;margin-bottom:18px;word-break:break-all}
@media(max-width:900px){.bio-card{overflow:auto}.bio-table{min-width:900px}}
</style>

<div class="bio-head">
    <div>
        <h1>Central Biometric Devices</h1>
        <div class="bio-sub">
            Institution / branch-wise Mantra MIS100V2 management
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('new_device_token'))
    <div class="bio-secret">
        <strong>Copy this token now. It is shown only after regeneration.</strong>
        <br><br>
        <strong>Device Code:</strong>
        {{ session('new_device_code') }}
        <br>
        <strong>Device Token:</strong>
        {{ session('new_device_token') }}
    </div>
@endif

<div class="bio-card">
<table class="bio-table">
<thead>
<tr>
    <th>Institution</th>
    <th>Branch</th>
    <th>Device</th>
    <th>Model</th>
    <th>Heartbeat</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>

@forelse($devices as $device)
<tr>
    <td>{{ $device->institution?->name ?? '-' }}</td>
    <td>{{ $device->branch?->name ?? 'Unassigned' }}</td>
    <td>
        <strong>{{ $device->device_code }}</strong><br>
        <small>{{ $device->name }}</small>
    </td>
    <td>{{ data_get($device->metadata, 'device_model', 'Mantra MIS100V2') }}</td>
    <td>
        {{ $device->last_seen_at
            ? $device->last_seen_at->format('d-m-Y h:i A')
            : 'Never' }}
    </td>
    <td>
        <span class="{{ $device->is_active ? 'bio-ok' : 'bio-off' }}">
            {{ $device->is_active ? 'ACTIVE' : 'INACTIVE' }}
        </span>
    </td>
    <td>
        @if(auth()->user()?->isMasterAdmin())
        <form method="POST"
              action="{{ route('admin.biometric-devices.toggle',$device) }}"
              style="display:inline">
            @csrf
            <button class="bio-btn bio-toggle">
                {{ $device->is_active ? 'Disable' : 'Enable' }}
            </button>
        </form>

        <form method="POST"
              action="{{ route('admin.biometric-devices.token',$device) }}"
              style="display:inline"
              onsubmit="return confirm('Regenerate this Device Token? Existing connector configuration will stop authenticating until updated.')">
            @csrf
            <button class="bio-btn bio-token">
                Regenerate Token
            </button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="7">
        No biometric devices registered.
    </td>
</tr>
@endforelse

</tbody>
</table>
</div>

@endsection
