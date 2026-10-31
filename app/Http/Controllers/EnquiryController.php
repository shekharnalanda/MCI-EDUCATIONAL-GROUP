<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        
        $verified = $request->session()->get('enquiry_email_verified');
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        if (!$verified || !hash_equals((string) ($verified['email'] ?? ''), $email) || now()->timestamp > (int) ($verified['expires_at'] ?? 0)) {
            return back()->withInput()->withErrors(['email' => 'पहले इस email पर भेजा गया OTP verify करें।']);
        }
        $request->session()->forget('enquiry_email_verified');

$customer = Customer::firstOrNew(['mobile' => $data['phone']]);
        $customer->name = $data['name'];
        if (!empty($data['email'])) $customer->email = $data['email'];
        $customer->last_activity_at = now();
        $customer->save();

        $data['customer_id'] = $customer->id;
        $data['status'] = 'new';
        $data['source_site'] = 'mciedu.in';
        $data['received_at'] = now();
        $data['sync_status'] = 'local';
        $data['auto_reply_status'] = 'disabled';
        $enquiry = Enquiry::create($data);

        try {
            $to = config('enquiry.recipient');
            if ($to) {
                $subject = 'New website enquiry: '.($enquiry->subject ?: $enquiry->name);
                $body = implode("\n", [
                    'A new enquiry was submitted on mciedu.in.', '',
                    'Name: '.$enquiry->name,
                    'Phone: '.($enquiry->phone ?: '-'),
                    'Email: '.($enquiry->email ?: '-'),
                    'Subject: '.($enquiry->subject ?: '-'), '',
                    'Message:', $enquiry->message ?: '-', '',
                    'Enquiry ID: '.$enquiry->id,
                ]);
                Mail::raw($body, function ($message) use ($to, $subject, $enquiry) {
                    $message->to($to)->subject($subject);
                    if ($enquiry->email) $message->replyTo($enquiry->email, $enquiry->name);
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Enquiry email notification failed', ['enquiry_id' => $enquiry->id, 'error' => $e->getMessage()]);
        }

        if ($enquiry->email) {
            try {
                Mail::raw('Thank you for contacting MCI Educational Group. We have received your enquiry and our team will get back to you soon.', function ($message) use ($enquiry) {
                    $message->to($enquiry->email)->subject('MCI Educational Group - Enquiry Received');
                });
            } catch (\\Throwable $e) {
                Log::warning('Enquiry confirmation email failed', ['enquiry_id' => $enquiry->id, 'error' => $e->getMessage()]);
            }
        }

        return back()->with('success', 'Thank you. Your enquiry has been submitted successfully.');
    }
}
