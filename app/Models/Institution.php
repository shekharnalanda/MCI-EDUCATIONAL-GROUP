<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = ['name','slug','website_url','short_description','description','logo','image','display_order','is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
