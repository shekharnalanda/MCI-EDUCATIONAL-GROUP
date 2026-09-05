<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    protected $fillable = [
        'institution_id', 'attendance_code', 'admission_number', 'roll_number', 'name',
        'course_class', 'batch_section', 'photo_path', 'mobile', 'status', 'metadata',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function institution() { return $this->belongsTo(Institution::class); }
    public function irisTemplates() { return $this->hasMany(IrisTemplate::class); }
    public function attendanceRecords() { return $this->hasMany(AttendanceRecord::class); }
}
