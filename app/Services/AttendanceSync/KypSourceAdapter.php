<?php

namespace App\Services\AttendanceSync;

use RuntimeException;

final class KypSourceAdapter implements AttendanceSourceAdapter
{
    public function persons(AttendanceSource $source): iterable
    {
        $export = $source->configuration['export_file'] ?? null;

        if (!$export || !is_file($export)) {
            throw new RuntimeException('KYP secure roster export is unavailable.');
        }

        $rows = json_decode(
            file_get_contents($export),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        foreach ($rows as $row) {
            yield new SourcePerson(
                externalId: (string) $row['source_user_id'],
                personType: 'student',
                name: (string) $row['name'],
                admissionNumber: filled($row['student_id'] ?? null)
                    ? (string) $row['student_id']
                    : 'KYP-USER-'.$row['source_user_id'],
                mobile: $row['phone'] ?? null,
                leftTemplate: $row['left_template'] ?? null,
                rightTemplate: $row['right_template'] ?? null,
            );
        }
    }
}
