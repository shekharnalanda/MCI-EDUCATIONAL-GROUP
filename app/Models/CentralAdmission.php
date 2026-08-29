<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentralAdmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institution_id','customer_id','enquiry_id','source_site','source_reference_id','application_reference',
        'applicant_name','phone','email','course_program','status','payment_status','metadata','submitted_at',
    ];

    protected $casts = ['metadata' => 'array', 'submitted_at' => 'datetime'];

    public function institution() { return $this->belongsTo(Institution::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function enquiry() { return $this->belongsTo(Enquiry::class); }
}
