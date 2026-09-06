<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BiometricActivationController extends Controller
{
    public function activate(Request $request)
    {
        $data = $request->validate([
            'device_code' => ['required','string','max:100'],
            'activation_code' => ['required','string','max:50'],
            'installation_id' => ['required','string','max:100'],
            'hardware' => ['nullable','array'],
            'agent_version' => ['nullable','string','max:50'],
        ]);

        $device = AttendanceDevice::query()
            ->where('device_code',$data['device_code'])
            ->where('is_active',true)
            ->firstOrFail();

        abort_unless(
            $device->activation_code_hash &&
            hash_equals(
                $device->activation_code_hash,
                hash('sha256',$data['activation_code'])
            ),
            403,
            'Invalid activation code.'
        );

        abort_if(
            $device->activation_expires_at &&
            now()->greaterThan($device->activation_expires_at),
            403,
            'Activation code expired.'
        );

        /*
         * Prevent silent takeover of an already activated
         * installation. Master Admin must reissue activation
         * before moving the device to another PC.
         */
        abort_if(
            $device->installation_id &&
            $device->installation_id !== $data['installation_id'],
            409,
            'Device is already activated on another installation.'
        );

        $plainToken='mci_iris_'.Str::random(56);

        $device->token_hash=hash('sha256',$plainToken);
        $device->installation_id=$data['installation_id'];
        $device->activated_at=now();
        $device->activation_code_hash=null;
        $device->activation_expires_at=null;

        $metadata=(array)($device->metadata ?? []);
        $metadata['hardware']=$data['hardware'] ?? [];
        $metadata['agent_version']=$data['agent_version'] ?? null;
        $metadata['auto_provisioned']=true;

        $device->metadata=$metadata;
        $device->save();

        return response()->json([
            'ok'=>true,
            'device_code'=>$device->device_code,
            'device_token'=>$plainToken,
            'institution_id'=>$device->institution_id,
            'branch_id'=>$device->attendance_branch_id,
            'server_time'=>now()->toIso8601String(),
        ]);
    }
}
