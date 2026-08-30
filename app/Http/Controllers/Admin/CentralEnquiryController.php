<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\Enquiry;
use App\Models\FollowUp;
use App\Models\Institution;
use App\Services\AdminAccessScope;
use App\Services\AuditLogger;
use App\Services\OutboundMessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CentralEnquiryController extends Controller
{
    public function index(Request $request, AdminAccessScope $scope)
    {
        $query = $scope->applyInstitutionScope(Enquiry::with(['institution','customer','assignedUser'])->latest(), $request->user());
        if ($request->boolean('trash')) $query->onlyTrashed();
        if ($request->filled('institution_id')) $query->where('institution_id', $request->integer('institution_id'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('auto_reply_status')) $query->where('auto_reply_status', $request->string('auto_reply_status'));
        if ($request->filled('category')) $query->where('category', $request->string('category'));
        if ($request->filled('q')) {
            $term = trim((string)$request->q);
            $query->where(fn($q) => $q->where('name','like',"%{$term}%")->orWhere('phone','like',"%{$term}%")->orWhere('email','like',"%{$term}%")->orWhere('subject','like',"%{$term}%")->orWhere('message','like',"%{$term}%"));
        }
        $institutions = Institution::orderBy('display_order')->orderBy('name');
        if (!$request->user()->isMasterAdmin() && $request->user()->role !== 'central_manager') $institutions->whereKey($request->user()->institution_id ?: 0);
        return view('admin.enquiries.index', [
            'items' => $query->paginate(25)->withQueryString(),
            'institutions' => $institutions->get(),
            'categories' => Enquiry::whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'canDelete' => $scope->canDelete($request->user()),
        ]);
    }

    private function authorizeRecord(Request $request, Enquiry $enquiry, AdminAccessScope $scope): void
    {
        abort_unless($scope->canAccessInstitution($request->user(), $enquiry->institution_id), 403);
    }

    public function show(Request $request, Enquiry $enquiry, AdminAccessScope $scope, OutboundMessagingService $messaging)
    {
        $this->authorizeRecord($request, $enquiry, $scope);
        $enquiry->load(['institution','customer','assignedUser','communicationLogs'=>fn($q)=>$q->latest(),'followUps'=>fn($q)=>$q->latest()]);
        $messagingStatus = $messaging->status();
        return view('admin.enquiries.show', compact('enquiry','messagingStatus'));
    }

    public function update(Request $request, Enquiry $enquiry, AdminAccessScope $scope, AuditLogger $audit)
    {
        $this->authorizeRecord($request, $enquiry, $scope);
        $data = $request->validate(['status'=>'required|in:new,auto_replied,manual_review,replied,follow_up_due,in_progress,converted,admitted,no_response,closed','priority'=>'nullable|in:low,normal,high,urgent','category'=>'nullable|string|max:80','course_service'=>'nullable|string|max:255']);
        $old = $enquiry->only(array_keys($data)); $enquiry->update($data); $audit->record('enquiry.updated',$enquiry,$old,$data);
        return back()->with('success','Enquiry updated.');
    }

    public function reply(Request $request, Enquiry $enquiry, AdminAccessScope $scope, AuditLogger $audit, OutboundMessagingService $messaging)
    {
        $this->authorizeRecord($request, $enquiry, $scope);
        $data = $request->validate([
            'channel'=>'required|in:email,whatsapp,sms',
            'subject'=>'nullable|string|max:180',
            'message'=>'required|string|max:10000',
        ]);

        $channel = $data['channel'];
        $institution = $enquiry->institution;
        $fromAddress = $institution?->sender_email ?: config('mail.from.address');
        $fromName = $institution?->sender_name ?: ($institution?->name ?: config('mail.from.name'));
        $subject = $data['subject'] ?: 'Re: '.($enquiry->subject ?: 'Your enquiry');
        $recipient = $channel === 'email' ? $enquiry->email : $enquiry->phone;

        if ($channel === 'email' && !$enquiry->email) return back()->withErrors(['message'=>'Customer email is not available for this enquiry.']);
        if (in_array($channel, ['whatsapp','sms'], true) && !$enquiry->phone) return back()->withErrors(['message'=>'Customer phone number is not available for this enquiry.']);
        if ($channel === 'whatsapp' && !$messaging->whatsappReady()) return back()->withErrors(['message'=>'WhatsApp is not configured or enabled yet.']);
        if ($channel === 'sms' && !$messaging->smsReady()) return back()->withErrors(['message'=>'SMS is not configured, DLT-ready, or enabled yet.']);

        $log = CommunicationLog::create([
            'enquiry_id'=>$enquiry->id,
            'customer_id'=>$enquiry->customer_id,
            'institution_id'=>$enquiry->institution_id,
            'user_id'=>auth()->id(),
            'channel'=>$channel,
            'direction'=>'outgoing',
            'reply_type'=>'manual',
            'subject'=>$channel === 'email' ? $subject : null,
            'message_body'=>$data['message'],
            'sender'=>$channel === 'email' ? $fromAddress : $fromName,
            'recipient'=>$recipient,
            'delivery_status'=>'pending',
        ]);

        try {
            $providerReference = null;

            if ($channel === 'email') {
                Mail::raw($data['message'], function($message) use($enquiry,$subject,$fromAddress,$fromName,$institution){
                    $message->to($enquiry->email,$enquiry->name)->subject($subject);
                    if($fromAddress)$message->from($fromAddress,$fromName);
                    if($institution?->reply_to_email)$message->replyTo($institution->reply_to_email,$fromName);
                });
            } elseif ($channel === 'whatsapp') {
                $result = $messaging->sendWhatsAppTemplate((string) $enquiry->phone, $data['message']);
                $providerReference = $result['provider_reference'] ?? null;
            } else {
                $result = $messaging->sendSms((string) $enquiry->phone, $data['message']);
                $providerReference = $result['provider_reference'] ?? null;
            }

            $log->update(['delivery_status'=>'sent','sent_at'=>now(),'provider_reference'=>$providerReference]);
            $enquiry->update(['status'=>'replied','last_replied_at'=>now()]);
            $audit->record('enquiry.replied',$enquiry,[],['communication_log_id'=>$log->id,'channel'=>$channel]);
        } catch (\Throwable $e) {
            $log->update(['delivery_status'=>'failed','failed_reason'=>$e->getMessage()]);
            Log::warning('Central enquiry manual reply failed',['enquiry_id'=>$enquiry->id,'channel'=>$channel,'error'=>$e->getMessage()]);
            return back()->withErrors(['message'=>ucfirst($channel).' reply could not be sent. It has been logged as failed for review.']);
        }

        return back()->with('success',ucfirst($channel).' reply sent and communication history updated.');
    }

    public function scheduleFollowUp(Request $request, Enquiry $enquiry, AdminAccessScope $scope, AuditLogger $audit)
    {
        $this->authorizeRecord($request,$enquiry,$scope); $data=$request->validate(['scheduled_at'=>'required|date','note'=>'nullable|string|max:3000']);
        $follow=FollowUp::create(['enquiry_id'=>$enquiry->id,'assigned_user_id'=>auth()->id(),'scheduled_at'=>$data['scheduled_at'],'note'=>$data['note']??null]); $enquiry->update(['next_follow_up_at'=>$data['scheduled_at']]); $audit->record('follow_up.scheduled',$enquiry,[],['follow_up_id'=>$follow->id,'scheduled_at'=>$data['scheduled_at']]);
        return back()->with('success','Follow-up scheduled.');
    }

    public function destroy(Request $request, Enquiry $enquiry, AdminAccessScope $scope, AuditLogger $audit)
    {
        $this->authorizeRecord($request,$enquiry,$scope); abort_unless($scope->canDelete($request->user()),403); $audit->record('enquiry.trashed',$enquiry); $enquiry->delete();
        return redirect()->route('admin.enquiries.index')->with('success','Enquiry moved to trash.');
    }

    public function restore(Request $request, int $enquiry, AdminAccessScope $scope, AuditLogger $audit)
    {
        $record=Enquiry::onlyTrashed()->findOrFail($enquiry); $this->authorizeRecord($request,$record,$scope); abort_unless($scope->canDelete($request->user()),403); $record->restore(); $audit->record('enquiry.restored',$record);
        return back()->with('success','Enquiry restored.');
    }
}
