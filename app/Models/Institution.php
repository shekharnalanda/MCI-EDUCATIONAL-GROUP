<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name','slug','website_url','sender_name','sender_email','reply_to_email','phone',
        'short_description','description','logo','image','display_order','is_active',
        'auto_reply_enabled','sync_enabled','api_token_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_reply_enabled' => 'boolean',
        'sync_enabled' => 'boolean',
    ];

    protected $hidden = ['api_token_hash'];

    public function users() { return $this->hasMany(User::class); }
    public function customers() { return $this->hasMany(Customer::class, 'first_institution_id'); }
    public function enquiries() { return $this->hasMany(Enquiry::class); }
    public function replyTemplates() { return $this->hasMany(ReplyTemplate::class); }
    public function autoReplyRules() { return $this->hasMany(AutoReplyRule::class); }
    public function communications() { return $this->hasMany(CommunicationLog::class); }
    public function attendanceStudents() { return $this->hasMany(AttendanceStudent::class); }
    public function attendanceDevices() { return $this->hasMany(AttendanceDevice::class); }
    public function attendanceRecords() { return $this->hasMany(AttendanceRecord::class); }
}
