<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\Enquiry;
use App\Models\FollowUp;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CentralEnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['institution','customer','assignedUser'])->latest();

        if ($request->filled('institution_id')) $query->where('institution_id', $request->integer('institution_id'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('auto_reply_status')) $query->where('auto_reply_status', $request->string('auto_reply_status'));
        if ($request->filled('category')) $query->where('category', $request->string('category'));
        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where(function ($q) use ($term) {
                $q->where('name','like',"%{$term}%")
                    ->orWhere('phone','like',"%{$term}%")
                    ->orWhere('email','like',"%{$term}%")
                    ->orWhere('subject','like',"%{$term}%")
                    ->orWhere('message','like',"%{$term}%");
            });
        }

        return view('admin.enquiries.index', [
            'items' => $query->paginate(25)->withQueryString(),
            'institutions' => Institution::orderBy('display_order')->orderBy('name')->get(),
            'categories' => Enquiry::whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['institution','customer','assignedUser','communicationLogs' => fn($q) => $q->latest(), 'followUps' => fn($q) => $q->latest()]);
        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'status' => 'required|in:new,auto_replied,manual_review,replied,follow_up_due,in_progress,converted,admitted,no_response,closed',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'category' => 'nullable|string|max:80',
            'course_service' => 'nullable|string|max:255',
        ]);
        $enquiry->update($data);
        return back()->with('success','Enquiry updated.');
    }

    public function reply(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'subject' => 'nullable|string|max:180',
            'message' => 'required|string|max:10000',
        ]);

        if (!$enquiry->email) return back()->withErrors(['message' => 'Customer email is not available for this enquiry.']);

        $institution = $enquiry->institution;
        $fromAddress = $institution?->sender_email ?: config('mail.from.address');
        $fromName = $institution?->sender_name ?: ($institution?->name ?: config('mail.from.name'));
        $subject = $data['subject'] ?: 'Re: '.($enquiry->subject ?: 'Your enquiry');

        $log = CommunicationLog::create([
            'enquiry_id' => $enquiry->id,
            'customer_id' => $enquiry->customer_id,
            'institution_id' => $enquiry->institution_id,
            'user_id' => auth()->id(),
            'channel' => 'email',
            'direction' => 'outgoing',
            'reply_type' => 'manual',
            'subject' => $subject,
            'message_body' => $data['message'],
            'sender' => $fromAddress,
            'recipient' => $enquiry->email,
            'delivery_status' => 'pending',
        ]);

        try {
            Mail::raw($data['message'], function ($message) use ($enquiry, $subject, $fromAddress, $fromName, $institution) {
                $message->to($enquiry->email, $enquiry->name)->subject($subject);
                if ($fromAddress) $message->from($fromAddress, $fromName);
                if ($institution?->reply_to_email) $message->replyTo($institution->reply_to_email, $fromName);
            });
            $log->update(['delivery_status' => 'sent', 'sent_at' => now()]);
            $enquiry->update(['status' => 'replied', 'last_replied_at' => now()]);
        } catch (\Throwable $e) {
            $log->update(['delivery_status' => 'failed', 'failed_reason' => $e->getMessage()]);
            Log::warning('Central enquiry manual reply failed', ['enquiry_id' => $enquiry->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['message' => 'Reply could not be sent. It has been logged as failed for review.']);
        }

        return back()->with('success','Reply sent and communication history updated.');
    }

    public function scheduleFollowUp(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate(['scheduled_at' => 'required|date', 'note' => 'nullable|string|max:3000']);
        FollowUp::create([
            'enquiry_id' => $enquiry->id,
            'assigned_user_id' => auth()->id(),
            'scheduled_at' => $data['scheduled_at'],
            'note' => $data['note'] ?? null,
        ]);
        $enquiry->update(['next_follow_up_at' => $data['scheduled_at']]);
        return back()->with('success','Follow-up scheduled.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        return redirect()->route('admin.enquiries.index')->with('success','Enquiry moved to trash.');
    }
}
