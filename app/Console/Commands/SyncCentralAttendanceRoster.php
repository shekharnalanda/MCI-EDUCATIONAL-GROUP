<?php

namespace App\Console\Commands;

use App\Models\AttendanceDevice;
use App\Services\AttendanceSync\AttendanceSource;
use App\Services\AttendanceSync\CentralRosterSynchronizer;
use App\Services\AttendanceSync\KypSourceAdapter;
use Illuminate\Console\Command;
use RuntimeException;

class SyncCentralAttendanceRoster extends Command
{
    protected $signature =
        'mci:attendance-sync {source} {--export=}';

    protected $description =
        'Synchronize an institution roster and existing iris enrollments into MCI Central Attendance';

    public function handle(
        CentralRosterSynchronizer $sync
    ): int {
        $sourceCode = strtoupper(
            (string) $this->argument('source')
        );

        if ($sourceCode !== 'KYP') {
            throw new RuntimeException(
                "Adapter not registered yet: {$sourceCode}"
            );
        }

        $device = AttendanceDevice::query()
            ->where('device_code', 'KYP-IRIS-01')
            ->where('is_active', true)
            ->firstOrFail();

        $export = (string) $this->option('export');

        $source = new AttendanceSource(
            code: 'KYP',
            institutionId: (int) $device->institution_id,
            branchId: $device->attendance_branch_id
                ? (int) $device->attendance_branch_id
                : null,
            driver: 'kyp_secure_export',
            configuration: ['export_file' => $export],
        );

        $stats = $sync->sync(
            $source,
            new KypSourceAdapter(),
            $device->id
        );

        $this->info('Central attendance roster sync = PASS');
        $this->line('Created: '.$stats['created']);
        $this->line('Updated: '.$stats['updated']);
        $this->line('Iris created: '.$stats['iris_created']);
        $this->line('Iris updated: '.$stats['iris_updated']);

        return self::SUCCESS;
    }
}
