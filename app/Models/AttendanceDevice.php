<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDevice extends Model
{
    protected $fillable = [
        'institution_id', 'name', 'device_code', 'serial_number', 'location',
        'token_hash', 'is_active', 'last_seen_at', 'metadata',
    ];

    protected $hidden = ['token_hash'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function institution() { return $this->belongsTo(Institution::class); }
    public function records() { return $this->hasMany(AttendanceRecord::class); }
}
