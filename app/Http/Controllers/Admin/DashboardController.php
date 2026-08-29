<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralAdmission;
use App\Models\Download;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\Institution;
use App\Models\NewsPost;
use App\Services\AdminAccessScope;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, AdminAccessScope $scope)
    {
        $enquiries=$scope->applyInstitutionScope(Enquiry::query(),$request->user());
        $admissions=$scope->applyInstitutionScope(CentralAdmission::query(),$request->user());
        $attention=$scope->applyInstitutionScope(Enquiry::with('institution'),$request->user())
            ->where(function($q){$q->where('auto_reply_status','manual_review')->orWhere('sync_status','failed')->orWhere(function($q){$q->whereNotNull('next_follow_up_at')->where('next_follow_up_at','<=',now())->whereNotIn('status',['closed','converted','admitted']);});})
            ->latest()->limit(10)->get();

        return view('admin.dashboard',[
            'institutionCount'=>$request->user()->isMasterAdmin()||$request->user()->role==='central_manager' ? Institution::count() : ($request->user()->institution_id?1:0),
            'newsCount'=>$request->user()->isMasterAdmin()?NewsPost::count():0,
            'galleryCount'=>$request->user()->isMasterAdmin()?GalleryItem::count():0,
            'downloadCount'=>$request->user()->isMasterAdmin()?Download::count():0,
            'enquiryCount'=>(clone $enquiries)->count(),
            'admissionCount'=>(clone $admissions)->count(),
            'todayEnquiryCount'=>(clone $enquiries)->whereDate('created_at',today())->count(),
            'pendingReplyCount'=>(clone $enquiries)->whereIn('status',['new','manual_review'])->count(),
            'autoRepliedCount'=>(clone $enquiries)->where('auto_reply_status','sent')->count(),
            'manualReviewCount'=>(clone $enquiries)->where('auto_reply_status','manual_review')->count(),
            'followUpDueCount'=>(clone $enquiries)->whereNotNull('next_follow_up_at')->where('next_follow_up_at','<=',now())->whereNotIn('status',['closed','converted','admitted'])->count(),
            'needsAttention'=>$attention,
        ]);
    }
}
