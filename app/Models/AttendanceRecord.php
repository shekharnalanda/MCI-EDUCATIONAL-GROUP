<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'institution_id', 'attendance_student_id', 'attendance_device_id', 'event_uuid',
        'attendance_date', 'session_key', 'captured_at', 'received_at', 'method', 'status',
        'match_score', 'quality_score', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'captured_at' => 'datetime',
            'received_at' => 'datetime',
            'match_score' => 'decimal:4',
            'quality_score' => 'decimal:3',
            'metadata' => 'array',
        ];
    }

    public function institution() { return $this->belongsTo(Institution::class); }
    public function student() { return $this->belongsTo(AttendanceStudent::class, 'attendance_student_id'); }
    public function device() { return $this->belongsTo(AttendanceDevice::class, 'attendance_device_id'); }
}
