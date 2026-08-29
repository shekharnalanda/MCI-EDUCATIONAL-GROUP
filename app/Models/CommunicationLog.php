<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
    protected $fillable = ['enquiry_id','customer_id','institution_id','user_id','auto_reply_rule_id','channel','direction','reply_type','subject','message_body','sender','recipient','delivery_status','provider_reference','failed_reason','sent_at'];
    protected $casts = ['sent_at' => 'datetime'];

    public function enquiry() { return $this->belongsTo(Enquiry::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function institution() { return $this->belongsTo(Institution::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function autoReplyRule() { return $this->belongsTo(AutoReplyRule::class); }
}
