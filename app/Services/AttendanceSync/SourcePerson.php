<?php

namespace App\Services\AttendanceSync;

final class SourcePerson
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $personType,
        public readonly string $name,
        public readonly ?string $admissionNumber = null,
        public readonly ?string $rollNumber = null,
        public readonly ?string $employeeNumber = null,
        public readonly ?string $mobile = null,
        public readonly ?string $courseClass = null,
        public readonly ?string $batchSection = null,
        public readonly ?string $leftTemplate = null,
        public readonly ?string $rightTemplate = null,
    ) {}
}
