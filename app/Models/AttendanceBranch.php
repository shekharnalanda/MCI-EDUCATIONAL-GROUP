<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceBranch extends Model
{
    protected $fillable = [
        'institution_id',
        'name',
        'code',
        'address',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function people()
    {
        return $this->hasMany(AttendanceStudent::class, 'attendance_branch_id');
    }

    public function devices()
    {
        return $this->hasMany(AttendanceDevice::class, 'attendance_branch_id');
    }
}
