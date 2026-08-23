<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = ['title','description','file_path','external_url','display_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
