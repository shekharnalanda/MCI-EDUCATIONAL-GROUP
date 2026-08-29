<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplyTemplate extends Model
{
    protected $fillable = ['institution_id','name','category','language','subject','body','placeholders','version','status','is_active'];
    protected $casts = ['placeholders' => 'array', 'is_active' => 'boolean'];

    public function institution() { return $this->belongsTo(Institution::class); }
    public function rules() { return $this->hasMany(AutoReplyRule::class); }
}
