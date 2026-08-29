<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralAdmission;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Institution;
use App\Services\AutoReplyEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentralEnquirySyncController extends Controller
{
    public function store(Request $request, AutoReplyEngine $autoReplyEngine)
    {
        $data = $request->validate([
            'business_code' => ['required','string','max:120'],
            'source_reference_id' => ['required','string','max:190'],
            'source_site' => ['required','string','max:255'],
            'name' => ['required','string','max:120'],
            'mobile' => ['nullable','string','max:30'],
            'phone' => ['nullable','string','max:30'],
            'email' => ['nullable','email','max:150'],
            'subject' => ['nullable','string','max:180'],
            'message' => ['required','string','max:5000'],
            'category' => ['nullable','string','max:80'],
            'course_service' => ['nullable','string','max:180'],
            'priority' => ['nullable','in:low,normal,high,urgent'],
            'submitted_at' => ['nullable','date'],
        ]);

        $institution = $this->authorizedInstitution($request, $data['business_code']);
        if ($institution instanceof \Illuminate\Http\JsonResponse) return $institution;

        $existing = Enquiry::withTrashed()->where('institution_id', $institution->id)
            ->where('source_reference_id', $data['source_reference_id'])->first();
        if ($existing) {
            return response()->json(['success'=>true,'duplicate'=>true,'central_reference_id'=>$existing->id,'sync_status'=>$existing->sync_status,'auto_reply_status'=>$existing->auto_reply_status]);
        }

        $phone = $data['mobile'] ?? $data['phone'] ?? null;
        $enquiry = DB::transaction(function () use ($data, $institution, $phone) {
            $customer = $this->upsertCustomer($institution, $data['name'], $phone, $data['email'] ?? null);
            return Enquiry::create([
                'institution_id'=>$institution->id,'customer_id'=>$customer->id,'name'=>$data['name'],'phone'=>$phone,
                'email'=>$data['email'] ?? null,'subject'=>$data['subject'] ?? null,'message'=>$data['message'],'status'=>'new',
                'source_site'=>$data['source_site'],'source_reference_id'=>$data['source_reference_id'],'category'=>$data['category'] ?? null,
                'course_service'=>$data['course_service'] ?? null,'priority'=>$data['priority'] ?? 'normal','auto_reply_status'=>'pending',
                'received_at'=>$data['submitted_at'] ?? now(),'sync_status'=>'synced',
            ]);
        });

        $auto = $autoReplyEngine->process($enquiry);
        $fresh = $enquiry->fresh();
        return response()->json(['success'=>true,'duplicate'=>false,'central_reference_id'=>$fresh->id,'sync_status'=>$fresh->sync_status,'auto_reply_status'=>$fresh->auto_reply_status,'auto_reply'=>$auto], 201);
    }

    public function admission(Request $request)
    {
        $data = $request->validate([
            'business_code'=>['required','string','max:120'],'source_reference_id'=>['required','string','max:190'],
            'source_site'=>['required','string','max:255'],'application_reference'=>['nullable','string','max:190'],
            'applicant_name'=>['required','string','max:120'],'mobile'=>['nullable','string','max:30'],'phone'=>['nullable','string','max:30'],
            'email'=>['nullable','email','max:150'],'course_program'=>['nullable','string','max:180'],'status'=>['nullable','string','max:40'],
            'payment_status'=>['nullable','string','max:40'],'submitted_at'=>['nullable','date'],'metadata'=>['nullable','array'],
        ]);

        $institution = $this->authorizedInstitution($request, $data['business_code']);
        if ($institution instanceof \Illuminate\Http\JsonResponse) return $institution;

        $existing = CentralAdmission::withTrashed()->where('institution_id',$institution->id)
            ->where('source_reference_id',$data['source_reference_id'])->first();
        if ($existing) return response()->json(['success'=>true,'duplicate'=>true,'central_reference_id'=>$existing->id]);

        $phone = $data['mobile'] ?? $data['phone'] ?? null;
        $admission = DB::transaction(function () use ($data,$institution,$phone) {
            $customer = $this->upsertCustomer($institution,$data['applicant_name'],$phone,$data['email'] ?? null);
            return CentralAdmission::create([
                'institution_id'=>$institution->id,'customer_id'=>$customer->id,'source_site'=>$data['source_site'],
                'source_reference_id'=>$data['source_reference_id'],'application_reference'=>$data['application_reference'] ?? null,
                'applicant_name'=>$data['applicant_name'],'phone'=>$phone,'email'=>$data['email'] ?? null,
                'course_program'=>$data['course_program'] ?? null,'status'=>$data['status'] ?? 'new','payment_status'=>$data['payment_status'] ?? null,
                'submitted_at'=>$data['submitted_at'] ?? now(),'metadata'=>$data['metadata'] ?? null,
            ]);
        });

        return response()->json(['success'=>true,'duplicate'=>false,'central_reference_id'=>$admission->id], 201);
    }

    private function authorizedInstitution(Request $request, string $businessCode)
    {
        $institution = Institution::where('slug',$businessCode)->where('is_active',true)->first();
        if (!$institution || !$institution->sync_enabled) return response()->json(['success'=>false,'message'=>'Business integration is not enabled.'],403);
        $token = (string)$request->header('X-MCI-Token');
        if (!$token || !$institution->api_token_hash || !hash_equals($institution->api_token_hash,hash('sha256',$token))) {
            return response()->json(['success'=>false,'message'=>'Invalid integration token.'],401);
        }
        return $institution;
    }

    private function upsertCustomer(Institution $institution, string $name, ?string $phone, ?string $email): Customer
    {
        $customer = $email ? Customer::where('email',$email)->first() : null;
        if (!$customer && $phone) $customer = Customer::where('mobile',$phone)->first();
        if (!$customer) return Customer::create(['name'=>$name,'mobile'=>$phone,'email'=>$email,'first_institution_id'=>$institution->id,'last_activity_at'=>now()]);
        $customer->update(['name'=>$customer->name ?: $name,'mobile'=>$customer->mobile ?: $phone,'email'=>$customer->email ?: $email,'last_activity_at'=>now()]);
        return $customer;
    }
}
