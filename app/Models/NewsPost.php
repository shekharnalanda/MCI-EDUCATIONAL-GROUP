<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsPost extends Model
{
    protected $fillable = ['title','slug','excerpt','content','image','published_at','is_active'];
    protected $casts = ['published_at' => 'datetime', 'is_active' => 'boolean'];
}
