<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\Institution;
use App\Models\NewsPost;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'institutionCount' => Institution::count(),
            'newsCount' => NewsPost::count(),
            'galleryCount' => GalleryItem::count(),
            'downloadCount' => Download::count(),
            'enquiryCount' => Enquiry::count(),
            'todayEnquiryCount' => Enquiry::whereDate('created_at', today())->count(),
            'pendingReplyCount' => Enquiry::whereIn('status', ['new','manual_review'])->count(),
            'autoRepliedCount' => Enquiry::where('auto_reply_status', 'sent')->count(),
            'manualReviewCount' => Enquiry::where('auto_reply_status', 'manual_review')->count(),
            'followUpDueCount' => Enquiry::whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<=', now())
                ->whereNotIn('status', ['closed','converted','admitted'])
                ->count(),
        ]);
    }
}
