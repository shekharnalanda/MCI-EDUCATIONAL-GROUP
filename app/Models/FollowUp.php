<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = ['enquiry_id','assigned_user_id','scheduled_at','completed_at','status','note','outcome'];
    protected $casts = ['scheduled_at' => 'datetime', 'completed_at' => 'datetime'];

    public function enquiry() { return $this->belongsTo(Enquiry::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
}
