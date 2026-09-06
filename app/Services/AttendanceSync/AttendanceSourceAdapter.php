<?php

namespace App\Services\AttendanceSync;

interface AttendanceSourceAdapter
{
    /** @return iterable<SourcePerson> */
    public function persons(AttendanceSource $source): iterable;
}
