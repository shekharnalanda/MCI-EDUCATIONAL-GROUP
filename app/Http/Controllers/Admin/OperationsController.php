<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Services\AdminAccessScope;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function customers(Request $request)
    {
        $query=Customer::withCount(['enquiries','admissions'])->latest('last_activity_at')->latest();
        $user=$request->user();
        if(!$user->isMasterAdmin()&&$user->role!=='central_manager'){
            $institutionId=$user->institution_id?:0;
            $query->where(function($q) use($institutionId){$q->whereHas('enquiries',fn($x)=>$x->where('institution_id',$institutionId))->orWhereHas('admissions',fn($x)=>$x->where('institution_id',$institutionId));});
        }
        if($request->filled('q')){$term=trim((string)$request->q);$query->where(fn($q)=>$q->where('name','like',"%{$term}%")->orWhere('mobile','like',"%{$term}%")->orWhere('email','like',"%{$term}%"));}
        return view('admin.customers.index',['items'=>$query->paginate(25)->withQueryString()]);
    }

    public function customer(Request $request, Customer $customer)
    {
        $user=$request->user(); if(!$user->isMasterAdmin()&&$user->role!=='central_manager'){$institutionId=$user->institution_id?:0;abort_unless($customer->enquiries()->where('institution_id',$institutionId)->exists()||$customer->admissions()->where('institution_id',$institutionId)->exists(),403);}
        $customer->load(['enquiries.institution','admissions.institution','communications'=>fn($q)=>$q->latest()]); return view('admin.customers.show',compact('customer'));
    }

    public function communications(Request $request, AdminAccessScope $scope)
    {
        $query=$scope->applyInstitutionScope(CommunicationLog::with(['institution','enquiry','customer','user'])->latest(),$request->user());
        if($request->filled('channel'))$query->where('channel',$request->string('channel')); if($request->filled('delivery_status'))$query->where('delivery_status',$request->string('delivery_status')); if($request->filled('reply_type'))$query->where('reply_type',$request->string('reply_type'));
        return view('admin.communications.index',['items'=>$query->paginate(30)->withQueryString()]);
    }

    public function followUps(Request $request)
    {
        $query=FollowUp::with(['enquiry.institution','assignedUser'])->orderByRaw("CASE WHEN status='pending' THEN 0 ELSE 1 END")->orderBy('scheduled_at'); $user=$request->user();
        if(!$user->isMasterAdmin()&&$user->role!=='central_manager')$query->whereHas('enquiry',fn($q)=>$q->where('institution_id',$user->institution_id?:0));
        if($request->filled('status'))$query->where('status',$request->string('status')); return view('admin.follow-ups.index',['items'=>$query->paginate(30)->withQueryString()]);
    }

    public function completeFollowUp(Request $request, FollowUp $followUp, AdminAccessScope $scope, AuditLogger $audit)
    {
        $followUp->loadMissing('enquiry'); abort_unless($scope->canAccessInstitution($request->user(),$followUp->enquiry?->institution_id),403);
        $data=$request->validate(['outcome'=>'nullable|string|max:255','note'=>'nullable|string|max:3000']); $old=$followUp->only(['status','completed_at','outcome','note']);
        $followUp->update(['status'=>'completed','completed_at'=>now(),'outcome'=>$data['outcome']??null,'note'=>$data['note']??$followUp->note]); if($followUp->enquiry)$followUp->enquiry->update(['next_follow_up_at'=>null]); $audit->record('follow_up.completed',$followUp,$old,$followUp->only(['status','completed_at','outcome','note']));
        return back()->with('success','Follow-up completed.');
    }
}
