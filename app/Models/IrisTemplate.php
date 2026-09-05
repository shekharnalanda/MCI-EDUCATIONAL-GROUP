<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrisTemplate extends Model
{
    protected $fillable = [
        'attendance_student_id', 'eye', 'template_data', 'template_hash', 'quality_score',
        'sdk_version', 'enrolled_device_id', 'enrolled_at', 'revoked_at',
    ];

    protected $hidden = ['template_data'];

    protected function casts(): array
    {
        return [
            'template_data' => 'encrypted',
            'quality_score' => 'decimal:3',
            'enrolled_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(AttendanceStudent::class, 'attendance_student_id'); }
    public function enrolledDevice() { return $this->belongsTo(AttendanceDevice::class, 'enrolled_device_id'); }
}
