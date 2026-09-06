<?php

namespace App\Services\AttendanceSync;

use App\Models\AttendanceStudent;
use App\Models\IrisTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class CentralRosterSynchronizer
{
    public function sync(
        AttendanceSource $source,
        AttendanceSourceAdapter $adapter,
        ?int $deviceId = null,
    ): array {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'iris_created' => 0,
            'iris_updated' => 0,
        ];

        DB::transaction(function () use (
            $source,
            $adapter,
            $deviceId,
            &$stats
        ) {
            foreach ($adapter->persons($source) as $incoming) {
                $person = AttendanceStudent::query()
                    ->where('institution_id', $source->institutionId)
                    ->where(
                        'metadata->source_system',
                        $source->code
                    )
                    ->where(
                        'metadata->source_external_id',
                        $incoming->externalId
                    )
                    ->first();

                if (!$person && $incoming->admissionNumber) {
                    $person = AttendanceStudent::query()
                        ->where('institution_id', $source->institutionId)
                        ->where(
                            'admission_number',
                            $incoming->admissionNumber
                        )
                        ->first();
                }

                if (!$person) {
                    $person = new AttendanceStudent();
                    $person->institution_id = $source->institutionId;
                    $person->attendance_code = (string) Str::uuid();
                    $stats['created']++;
                } else {
                    $stats['updated']++;
                }

                $person->attendance_branch_id = $source->branchId;
                $person->person_type = $incoming->personType;
                $person->name = $incoming->name;
                $person->admission_number = $incoming->admissionNumber;
                $person->roll_number = $incoming->rollNumber;
                $person->employee_number = $incoming->employeeNumber;
                $person->mobile = $incoming->mobile;
                $person->course_class = $incoming->courseClass;
                $person->batch_section = $incoming->batchSection;
                $person->status = 'active';

                $person->metadata = array_merge(
                    (array) ($person->metadata ?? []),
                    [
                        'source_system' => $source->code,
                        'source_external_id' => $incoming->externalId,
                        'central_sync' => 'v1',
                        'last_source_sync_at' => now()->toIso8601String(),
                    ]
                );

                $person->save();

                foreach ([
                    'left' => $incoming->leftTemplate,
                    'right' => $incoming->rightTemplate,
                ] as $eye => $template) {
                    if (!filled($template)) {
                        continue;
                    }

                    $decoded = base64_decode($template, true);

                    if ($decoded === false || strlen($decoded) < 32) {
                        throw new RuntimeException(
                            "Invalid iris payload: {$source->code}/".
                            "{$incoming->externalId}/{$eye}"
                        );
                    }

                    $iris = IrisTemplate::query()
                        ->where(
                            'attendance_student_id',
                            $person->id
                        )
                        ->where('eye', $eye)
                        ->first();

                    if (!$iris) {
                        $iris = new IrisTemplate();
                        $iris->attendance_student_id = $person->id;
                        $iris->eye = $eye;
                        $stats['iris_created']++;
                    } else {
                        $stats['iris_updated']++;
                    }

                    $iris->template_data = $template;
                    $iris->template_hash = hash('sha256', $decoded);
                    $iris->sdk_version =
                        'MIS100V2-'.$source->code.'-SYNC';
                    $iris->enrolled_device_id = $deviceId;
                    $iris->enrolled_at = now();
                    $iris->revoked_at = null;
                    $iris->save();

                    unset($decoded);
                }
            }
        });

        return $stats;
    }
}
