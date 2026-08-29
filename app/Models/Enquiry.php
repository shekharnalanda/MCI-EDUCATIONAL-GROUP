<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institution_id','customer_id','name','phone','email','subject','message','status',
        'source_site','source_reference_id','category','course_service','priority',
        'auto_reply_status','assigned_user_id','received_at','last_replied_at',
        'next_follow_up_at','sync_status',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'last_replied_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public function institution() { return $this->belongsTo(Institution::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function communications() { return $this->hasMany(CommunicationLog::class); }
    public function followUps() { return $this->hasMany(FollowUp::class); }
}
