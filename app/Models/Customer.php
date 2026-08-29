<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name','mobile','email','preferred_language','first_institution_id','status','last_activity_at'];

    protected $casts = ['last_activity_at' => 'datetime'];

    public function firstInstitution() { return $this->belongsTo(Institution::class, 'first_institution_id'); }
    public function enquiries() { return $this->hasMany(Enquiry::class); }
    public function communications() { return $this->hasMany(CommunicationLog::class); }
}
