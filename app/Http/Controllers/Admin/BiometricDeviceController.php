<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDevice;
use Illuminate\Support\Str;

class BiometricDeviceController extends Controller
{
    public function index()
    {
        $devices = AttendanceDevice::query()
            ->with(['institution', 'branch'])
            ->orderBy('institution_id')
            ->orderBy('attendance_branch_id')
            ->orderBy('device_code')
            ->get();

        return view(
            'admin.attendance.devices',
            compact('devices')
        );
    }

    public function toggle(
        AttendanceDevice $device
    ) {
        abort_unless(
            auth()->user()?->isMasterAdmin(),
            403
        );

        $device->is_active =
            ! $device->is_active;

        $device->save();

        return back()->with(
            'success',
            'Device status updated.'
        );
    }

    public function regenerateToken(
        AttendanceDevice $device
    ) {
        abort_unless(
            auth()->user()?->isMasterAdmin(),
            403
        );

        $token =
            'mci_iris_'.Str::random(56);

        $device->token_hash =
            hash('sha256', $token);

        $device->save();

        return back()
            ->with(
                'success',
                'New Device Token generated. Copy it now.'
            )
            ->with(
                'new_device_token',
                $token
            )
            ->with(
                'new_device_code',
                $device->device_code
            );
    }
}
