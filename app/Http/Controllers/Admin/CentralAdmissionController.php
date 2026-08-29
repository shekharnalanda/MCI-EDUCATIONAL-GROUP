<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralAdmission;
use App\Models\Institution;
use Illuminate\Http\Request;

class CentralAdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralAdmission::with(['institution','customer','enquiry'])->latest('submitted_at')->latest();
        if ($request->filled('institution_id')) $query->where('institution_id', $request->integer('institution_id'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('payment_status')) $query->where('payment_status', $request->string('payment_status'));
        if ($request->filled('q')) {
            $term = trim((string)$request->q);
            $query->where(function ($q) use ($term) {
                $q->where('applicant_name','like',"%{$term}%")
                    ->orWhere('phone','like',"%{$term}%")
                    ->orWhere('email','like',"%{$term}%")
                    ->orWhere('course_program','like',"%{$term}%")
                    ->orWhere('application_reference','like',"%{$term}%");
            });
        }
        return view('admin.admissions.index', [
            'items' => $query->paginate(25)->withQueryString(),
            'institutions' => Institution::orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function show(CentralAdmission $admission)
    {
        $admission->load(['institution','customer','enquiry']);
        return view('admin.admissions.show', compact('admission'));
    }

    public function update(Request $request, CentralAdmission $admission)
    {
        $data = $request->validate([
            'status' => 'required|in:new,under_review,approved,admitted,rejected,cancelled,closed',
            'payment_status' => 'required|in:unknown,pending,partial,paid,failed,refunded',
            'course_program' => 'nullable|string|max:180',
        ]);
        $admission->update($data);
        return back()->with('success','Admission updated.');
    }

    public function destroy(CentralAdmission $admission)
    {
        $admission->delete();
        return redirect()->route('admin.admissions.index')->with('success','Admission moved to trash.');
    }
}
