<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'institution_id', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function institution() { return $this->belongsTo(Institution::class); }
    public function assignedEnquiries() { return $this->hasMany(Enquiry::class, 'assigned_user_id'); }
    public function communications() { return $this->hasMany(CommunicationLog::class); }
    public function followUps() { return $this->hasMany(FollowUp::class, 'assigned_user_id'); }

    public function isMasterAdmin(): bool
    {
        return $this->role === 'master_admin';
    }
}
