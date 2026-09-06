<?php

namespace App\Services\AttendanceSync;

final class AttendanceSource
{
    public function __construct(
        public readonly string $code,
        public readonly int $institutionId,
        public readonly ?int $branchId,
        public readonly string $driver,
        public readonly array $configuration = [],
    ) {}
}
