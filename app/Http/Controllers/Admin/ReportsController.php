<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralAdmission;
use App\Models\CommunicationLog;
use App\Models\Enquiry;
use App\Models\Institution;
use App\Services\AdminAccessScope;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request, AdminAccessScope $scope)
    {
        $user = $request->user();
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(29)->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        $enquiries = $scope->applyInstitutionScope(Enquiry::query(), $user)->whereBetween('created_at', [$from, $to]);
        $admissions = $scope->applyInstitutionScope(CentralAdmission::query(), $user)->whereBetween('created_at', [$from, $to]);
        $communications = $scope->applyInstitutionScope(CommunicationLog::query(), $user)->whereBetween('created_at', [$from, $to]);

        $totalEnquiries = (clone $enquiries)->count();
        $converted = (clone $enquiries)->whereIn('status', ['converted', 'admitted'])->count();
        $totalAdmissions = (clone $admissions)->count();
        $sentCommunications = (clone $communications)->where('delivery_status', 'sent')->count();
        $failedCommunications = (clone $communications)->where('delivery_status', 'failed')->count();
        $communicationTotal = $sentCommunications + $failedCommunications;

        $todayCount = $scope->applyInstitutionScope(Enquiry::query(), $user)->where('created_at', '>=', today())->count();
        $sevenDayCount = $scope->applyInstitutionScope(Enquiry::query(), $user)->where('created_at', '>=', now()->subDays(6)->startOfDay())->count();
        $thirtyDayCount = $scope->applyInstitutionScope(Enquiry::query(), $user)->where('created_at', '>=', now()->subDays(29)->startOfDay())->count();

        $institutionQuery = Institution::query()->orderBy('name');
        if (!($user->isMasterAdmin() || $user->role === 'central_manager')) {
            $institutionQuery->whereKey($user->institution_id);
        }

        $businessRows = $institutionQuery->get()->map(function ($institution) use ($from, $to) {
            $businessEnquiries = Enquiry::where('institution_id', $institution->id)->whereBetween('created_at', [$from, $to]);
            $businessAdmissions = CentralAdmission::where('institution_id', $institution->id)->whereBetween('created_at', [$from, $to]);
            $businessCommunications = CommunicationLog::where('institution_id', $institution->id)->whereBetween('created_at', [$from, $to]);

            $enquiryCount = (clone $businessEnquiries)->count();
            $convertedCount = (clone $businessEnquiries)->whereIn('status', ['converted', 'admitted'])->count();
            $sent = (clone $businessCommunications)->where('delivery_status', 'sent')->count();
            $failed = (clone $businessCommunications)->where('delivery_status', 'failed')->count();

            return (object) [
                'institution' => $institution,
                'enquiries' => $enquiryCount,
                'admissions' => (clone $businessAdmissions)->count(),
                'converted' => $convertedCount,
                'conversion_rate' => $enquiryCount ? round(($convertedCount / $enquiryCount) * 100, 1) : 0,
                'sent' => $sent,
                'failed' => $failed,
                'delivery_rate' => ($sent + $failed) ? round(($sent / ($sent + $failed)) * 100, 1) : 100,
            ];
        })->sortByDesc('enquiries')->values();

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'totalEnquiries' => $totalEnquiries,
            'totalAdmissions' => $totalAdmissions,
            'converted' => $converted,
            'conversionRate' => $totalEnquiries ? round(($converted / $totalEnquiries) * 100, 1) : 0,
            'sentCommunications' => $sentCommunications,
            'failedCommunications' => $failedCommunications,
            'communicationSuccessRate' => $communicationTotal ? round(($sentCommunications / $communicationTotal) * 100, 1) : 100,
            'todayCount' => $todayCount,
            'sevenDayCount' => $sevenDayCount,
            'thirtyDayCount' => $thirtyDayCount,
            'businessRows' => $businessRows,
        ]);
    }
}
