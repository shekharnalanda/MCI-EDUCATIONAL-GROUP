<?php

namespace App\Http\Controllers;

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
            'email' => ['nullable', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $data['status'] = 'new';
        $enquiry = Enquiry::create($data);

        try {
            $to = config('mail.from.address');
            if ($to) {
                $subject = 'New website enquiry: '.($enquiry->subject ?: $enquiry->name);
                $body = implode("\n", [
                    'A new enquiry was submitted on mciedu.in.',
                    '',
                    'Name: '.$enquiry->name,
                    'Phone: '.($enquiry->phone ?: '-'),
                    'Email: '.($enquiry->email ?: '-'),
                    'Subject: '.($enquiry->subject ?: '-'),
                    '',
                    'Message:',
                    $enquiry->message ?: '-',
                    '',
                    'Enquiry ID: '.$enquiry->id,
                ]);

                Mail::raw($body, function ($message) use ($to, $subject, $enquiry) {
                    $message->to($to)->subject($subject);
                    if ($enquiry->email) {
                        $message->replyTo($enquiry->email, $enquiry->name);
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Enquiry email notification failed', [
                'enquiry_id' => $enquiry->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Thank you. Your enquiry has been submitted successfully.');
    }
}
