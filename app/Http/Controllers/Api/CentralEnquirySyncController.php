<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $institution = Institution::where('slug', $data['business_code'])->where('is_active', true)->first();
        if (!$institution || !$institution->sync_enabled) {
            return response()->json(['success' => false, 'message' => 'Business integration is not enabled.'], 403);
        }

        $token = (string)$request->header('X-MCI-Token');
        if (!$token || !$institution->api_token_hash || !hash_equals($institution->api_token_hash, hash('sha256', $token))) {
            return response()->json(['success' => false, 'message' => 'Invalid integration token.'], 401);
        }

        $existing = Enquiry::withTrashed()
            ->where('institution_id', $institution->id)
            ->where('source_reference_id', $data['source_reference_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'duplicate' => true,
                'central_reference_id' => $existing->id,
                'sync_status' => $existing->sync_status,
                'auto_reply_status' => $existing->auto_reply_status,
            ]);
        }

        $phone = $data['mobile'] ?? $data['phone'] ?? null;

        $enquiry = DB::transaction(function () use ($data, $institution, $phone) {
            $customer = null;
            if ($data['email'] ?? null) {
                $customer = Customer::where('email', $data['email'])->first();
            }
            if (!$customer && $phone) {
                $customer = Customer::where('mobile', $phone)->first();
            }
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $data['name'],
                    'mobile' => $phone,
                    'email' => $data['email'] ?? null,
                    'first_institution_id' => $institution->id,
                    'last_activity_at' => now(),
                ]);
            } else {
                $customer->update([
                    'name' => $customer->name ?: $data['name'],
                    'mobile' => $customer->mobile ?: $phone,
                    'email' => $customer->email ?: ($data['email'] ?? null),
                    'last_activity_at' => now(),
                ]);
            }

            return Enquiry::create([
                'institution_id' => $institution->id,
                'customer_id' => $customer->id,
                'name' => $data['name'],
                'phone' => $phone,
                'email' => $data['email'] ?? null,
                'subject' => $data['subject'] ?? null,
                'message' => $data['message'],
                'status' => 'new',
                'source_site' => $data['source_site'],
                'source_reference_id' => $data['source_reference_id'],
                'category' => $data['category'] ?? null,
                'course_service' => $data['course_service'] ?? null,
                'priority' => $data['priority'] ?? 'normal',
                'auto_reply_status' => 'pending',
                'received_at' => $data['submitted_at'] ?? now(),
                'sync_status' => 'synced',
            ]);
        });

        $auto = $autoReplyEngine->process($enquiry);

        return response()->json([
            'success' => true,
            'duplicate' => false,
            'central_reference_id' => $enquiry->id,
            'sync_status' => $enquiry->fresh()->sync_status,
            'auto_reply_status' => $enquiry->fresh()->auto_reply_status,
            'auto_reply' => $auto,
        ], 201);
    }
}
