<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralAdmission;
use App\Models\CommunicationLog;
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
        $user = $request->user();
        $enquiries = $scope->applyInstitutionScope(Enquiry::query(), $user);
        $admissions = $scope->applyInstitutionScope(CentralAdmission::query(), $user);
        $communications = $scope->applyInstitutionScope(CommunicationLog::query(), $user);
        $attention = $scope->applyInstitutionScope(Enquiry::with('institution'), $user)
            ->where(function ($q) {
                $q->where('auto_reply_status', 'manual_review')
                    ->orWhere('sync_status', 'failed')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('next_follow_up_at')
                            ->where('next_follow_up_at', '<=', now())
                            ->whereNotIn('status', ['closed', 'converted', 'admitted']);
                    });
            })
            ->latest()->limit(10)->get();

        $todayEnquiries = (clone $enquiries)->where('created_at', '>=', today())->count();
        $sevenDayEnquiries = (clone $enquiries)->where('created_at', '>=', now()->subDays(6)->startOfDay())->count();
        $thirtyDayEnquiries = (clone $enquiries)->where('created_at', '>=', now()->subDays(29)->startOfDay())->count();
        $thirtyDayAdmissions = (clone $admissions)->where('created_at', '>=', now()->subDays(29)->startOfDay())->count();
        $thirtyDayConverted = (clone $enquiries)->where('created_at', '>=', now()->subDays(29)->startOfDay())->whereIn('status', ['converted', 'admitted'])->count();
        $sent30 = (clone $communications)->where('created_at', '>=', now()->subDays(29)->startOfDay())->where('delivery_status', 'sent')->count();
        $failed30 = (clone $communications)->where('created_at', '>=', now()->subDays(29)->startOfDay())->where('delivery_status', 'failed')->count();
        $communicationTotal = $sent30 + $failed30;

        $businessPerformance = collect();
        if ($user->isMasterAdmin() || $user->role === 'central_manager') {
            $businessPerformance = Institution::orderBy('name')->get()->map(function ($institution) {
                $from = now()->subDays(29)->startOfDay();
                $enquiryCount = Enquiry::where('institution_id', $institution->id)->where('created_at', '>=', $from)->count();
                $admissionCount = CentralAdmission::where('institution_id', $institution->id)->where('created_at', '>=', $from)->count();
                $sent = CommunicationLog::where('institution_id', $institution->id)->where('created_at', '>=', $from)->where('delivery_status', 'sent')->count();
                $failed = CommunicationLog::where('institution_id', $institution->id)->where('created_at', '>=', $from)->where('delivery_status', 'failed')->count();

                return (object) [
                    'institution' => $institution,
                    'enquiries' => $enquiryCount,
                    'admissions' => $admissionCount,
                    'sent' => $sent,
                    'failed' => $failed,
                    'delivery_rate' => ($sent + $failed) ? round(($sent / ($sent + $failed)) * 100, 1) : 100,
                ];
            })->sortByDesc('enquiries')->values();
        }

        return view('admin.dashboard', [
            'institutionCount' => $user->isMasterAdmin() || $user->role === 'central_manager' ? Institution::count() : ($user->institution_id ? 1 : 0),
            'newsCount' => $user->isMasterAdmin() ? NewsPost::count() : 0,
            'galleryCount' => $user->isMasterAdmin() ? GalleryItem::count() : 0,
            'downloadCount' => $user->isMasterAdmin() ? Download::count() : 0,
            'enquiryCount' => (clone $enquiries)->count(),
            'admissionCount' => (clone $admissions)->count(),
            'todayEnquiryCount' => $todayEnquiries,
            'sevenDayEnquiryCount' => $sevenDayEnquiries,
            'thirtyDayEnquiryCount' => $thirtyDayEnquiries,
            'thirtyDayAdmissionCount' => $thirtyDayAdmissions,
            'thirtyDayConversionRate' => $thirtyDayEnquiries ? round(($thirtyDayConverted / $thirtyDayEnquiries) * 100, 1) : 0,
            'communicationSuccessRate' => $communicationTotal ? round(($sent30 / $communicationTotal) * 100, 1) : 100,
            'failedCommunicationCount' => $failed30,
            'failedSyncCount' => (clone $enquiries)->where('sync_status', 'failed')->count(),
            'pendingReplyCount' => (clone $enquiries)->whereIn('status', ['new', 'manual_review'])->count(),
            'autoRepliedCount' => (clone $enquiries)->where('auto_reply_status', 'sent')->count(),
            'manualReviewCount' => (clone $enquiries)->where('auto_reply_status', 'manual_review')->count(),
            'followUpDueCount' => (clone $enquiries)->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now())->whereNotIn('status', ['closed', 'converted', 'admitted'])->count(),
            'businessPerformance' => $businessPerformance,
            'needsAttention' => $attention,
        ]);
    }
}
