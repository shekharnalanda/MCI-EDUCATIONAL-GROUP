<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralAdmission;
use App\Models\Institution;
use App\Services\AdminAccessScope;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CentralAdmissionController extends Controller
{
    public function index(Request $request, AdminAccessScope $scope)
    {
        $query = $scope->applyInstitutionScope(CentralAdmission::with(['institution','customer','enquiry'])->latest('submitted_at')->latest(), $request->user());
        if ($request->boolean('trash')) $query->onlyTrashed();
        if ($request->filled('institution_id')) $query->where('institution_id', $request->integer('institution_id'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('payment_status')) $query->where('payment_status', $request->string('payment_status'));
        if ($request->filled('q')) { $term=trim((string)$request->q); $query->where(fn($q)=>$q->where('applicant_name','like',"%{$term}%")->orWhere('phone','like',"%{$term}%")->orWhere('email','like',"%{$term}%")->orWhere('course_program','like',"%{$term}%")->orWhere('application_reference','like',"%{$term}%")); }
        $institutions=Institution::orderBy('display_order')->orderBy('name'); if(!$request->user()->isMasterAdmin()&&$request->user()->role!=='central_manager')$institutions->whereKey($request->user()->institution_id?:0);
        return view('admin.admissions.index',['items'=>$query->paginate(25)->withQueryString(),'institutions'=>$institutions->get(),'canDelete'=>$scope->canDelete($request->user())]);
    }

    private function authorizeRecord(Request $request, CentralAdmission $admission, AdminAccessScope $scope): void { abort_unless($scope->canAccessInstitution($request->user(),$admission->institution_id),403); }

    public function show(Request $request, CentralAdmission $admission, AdminAccessScope $scope)
    { $this->authorizeRecord($request,$admission,$scope); $admission->load(['institution','customer','enquiry']); return view('admin.admissions.show',compact('admission')); }

    public function update(Request $request, CentralAdmission $admission, AdminAccessScope $scope, AuditLogger $audit)
    {
        $this->authorizeRecord($request,$admission,$scope); $data=$request->validate(['status'=>'required|in:new,under_review,approved,admitted,rejected,cancelled,closed','payment_status'=>'required|in:unknown,pending,partial,paid,failed,refunded','course_program'=>'nullable|string|max:180']);
        $old=$admission->only(array_keys($data)); $admission->update($data); $audit->record('admission.updated',$admission,$old,$data); return back()->with('success','Admission updated.');
    }

    public function destroy(Request $request, CentralAdmission $admission, AdminAccessScope $scope, AuditLogger $audit)
    { $this->authorizeRecord($request,$admission,$scope); abort_unless($scope->canDelete($request->user()),403); $audit->record('admission.trashed',$admission); $admission->delete(); return redirect()->route('admin.admissions.index')->with('success','Admission moved to trash.'); }

    public function restore(Request $request, int $admission, AdminAccessScope $scope, AuditLogger $audit)
    { $record=CentralAdmission::onlyTrashed()->findOrFail($admission); $this->authorizeRecord($request,$record,$scope); abort_unless($scope->canDelete($request->user()),403); $record->restore(); $audit->record('admission.restored',$record); return back()->with('success','Admission restored.'); }
}
