<?php

namespace App\Console\Commands;

use App\Models\AttendanceBranch;
use App\Models\AttendanceDevice;
use Illuminate\Console\Command;

class AuditCentralBiometricProduction extends Command
{
    protected $signature =
        'mci:biometric-audit';

    protected $description =
        'Audit production readiness of MCI Central Biometric Attendance';

    public function handle(): int
    {
        $expected = [
            'MCI-MAIN-IRIS-01' => 1,
            'CNETCI-BS-IRIS-01' => 2,
            'PATH-BS-IRIS-01' => 3,
            'PATH-GR-IRIS-01' => 3,
            'KYP-IRIS-01' => 16,
        ];

        foreach($expected as $code => $institutionId){

            $device=AttendanceDevice::where(
                'device_code',$code
            )->first();

            if(!$device){
                $this->error(
                    'FAIL '.$code.' missing'
                );
                return self::FAILURE;
            }

            if(
                (int)$device->institution_id !==
                $institutionId
            ){
                $this->error(
                    'FAIL '.$code.' institution'
                );
                return self::FAILURE;
            }

            if(
                !$device->attendance_branch_id ||
                !$device->token_hash ||
                !$device->is_active
            ){
                $this->error(
                    'FAIL '.$code.' readiness'
                );
                return self::FAILURE;
            }

            $branch=AttendanceBranch::find(
                $device->attendance_branch_id
            );

            if(
                !$branch ||
                (int)$branch->institution_id !==
                $institutionId ||
                !$branch->is_active
            ){
                $this->error(
                    'FAIL '.$code.' branch'
                );
                return self::FAILURE;
            }

            $this->info(
                'PASS '.$code
            );
        }

        $this->info(
            'MCI_CENTRAL_BIOMETRIC_PRODUCTION=PASS'
        );

        return self::SUCCESS;
    }
}
