<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCheckoutCentralAttendanceCommand extends Command
{
    protected $signature = 'mci:attendance-auto-checkout';

    protected $description =
        'Auto Mark-Out open biometric attendance after 120 minutes.';

    public function handle(): int
    {
        $closed = 0;
        $cutoff = now()->subMinutes(120);

        AttendanceRecord::query()
            ->where('method', 'iris')
            ->where('status', 'present')
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->where('checked_in_at', '<=', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($records) use (&$closed): void {
                foreach ($records as $attendance) {
                    DB::transaction(function () use ($attendance, &$closed): void {
                        $record = AttendanceRecord::query()
                            ->lockForUpdate()
                            ->find($attendance->id);

                        if (
                            ! $record ||
                            $record->status !== 'present' ||
                            ! $record->checked_in_at ||
                            $record->checked_out_at
                        ) {
                            return;
                        }

                        $autoOut = $record->checked_in_at
                            ->copy()
                            ->addMinutes(120);

                        if ($autoOut->isFuture()) {
                            return;
                        }

                        $record->update([
                            'checked_out_at' => $autoOut,
                            'checkout_source' => 'system_auto',
                            'minutes_completed' => 120,
                            'status' => 'completed',
                        ]);

                        $closed++;
                    });
                }
            });

        $this->info("Central Auto Mark-Out completed: {$closed}");

        return self::SUCCESS;
    }
}
