<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoReplyRule extends Model
{
    protected $fillable = ['institution_id','reply_template_id','name','category','keywords','conditions','priority','auto_send','is_active','fallback_action'];
    protected $casts = ['keywords' => 'array', 'conditions' => 'array', 'auto_send' => 'boolean', 'is_active' => 'boolean'];

    public function institution() { return $this->belongsTo(Institution::class); }
    public function template() { return $this->belongsTo(ReplyTemplate::class, 'reply_template_id'); }
    public function communications() { return $this->hasMany(CommunicationLog::class); }
}
