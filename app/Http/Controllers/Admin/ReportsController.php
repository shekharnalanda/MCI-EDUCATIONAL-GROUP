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
        $from=$request->date('from')?->startOfDay() ?? now()->subDays(29)->startOfDay();
        $to=$request->date('to')?->endOfDay() ?? now()->endOfDay();
        $enquiries=$scope->applyInstitutionScope(Enquiry::query(),$request->user())->whereBetween('created_at',[$from,$to]);
        $admissions=$scope->applyInstitutionScope(CentralAdmission::query(),$request->user())->whereBetween('created_at',[$from,$to]);
        $communications=$scope->applyInstitutionScope(CommunicationLog::query(),$request->user())->whereBetween('created_at',[$from,$to]);
        $totalEnquiries=(clone $enquiries)->count(); $converted=(clone $enquiries)->whereIn('status',['converted','admitted'])->count();
        $businessRows=(clone $enquiries)->selectRaw('institution_id, count(*) as total')->groupBy('institution_id')->with('institution')->get();
        return view('admin.reports.index',[
            'from'=>$from,'to'=>$to,'totalEnquiries'=>$totalEnquiries,'totalAdmissions'=>(clone $admissions)->count(),
            'converted'=>$converted,'conversionRate'=>$totalEnquiries?round(($converted/$totalEnquiries)*100,1):0,
            'sentCommunications'=>(clone $communications)->where('delivery_status','sent')->count(),
            'failedCommunications'=>(clone $communications)->where('delivery_status','failed')->count(),
            'businessRows'=>$businessRows,
        ]);
    }
}
