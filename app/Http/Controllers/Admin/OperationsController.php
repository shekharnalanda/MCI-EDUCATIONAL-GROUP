<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\Customer;
use App\Models\FollowUp;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function customers(Request $request)
    {
        $query = Customer::withCount(['enquiries','admissions'])->latest('last_activity_at')->latest();
        if ($request->filled('q')) {
            $term = trim((string)$request->q);
            $query->where(function ($q) use ($term) {
                $q->where('name','like',"%{$term}%")->orWhere('mobile','like',"%{$term}%")->orWhere('email','like',"%{$term}%");
            });
        }
        return view('admin.customers.index', ['items' => $query->paginate(25)->withQueryString()]);
    }

    public function customer(Customer $customer)
    {
        $customer->load(['enquiries.institution','admissions.institution','communications' => fn($q) => $q->latest()]);
        return view('admin.customers.show', compact('customer'));
    }

    public function communications(Request $request)
    {
        $query = CommunicationLog::with(['institution','enquiry','customer','user'])->latest();
        if ($request->filled('channel')) $query->where('channel', $request->string('channel'));
        if ($request->filled('delivery_status')) $query->where('delivery_status', $request->string('delivery_status'));
        if ($request->filled('reply_type')) $query->where('reply_type', $request->string('reply_type'));
        return view('admin.communications.index', ['items' => $query->paginate(30)->withQueryString()]);
    }

    public function followUps(Request $request)
    {
        $query = FollowUp::with(['enquiry.institution','assignedUser'])->orderByRaw("CASE WHEN status='pending' THEN 0 ELSE 1 END")->orderBy('scheduled_at');
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        return view('admin.follow-ups.index', ['items' => $query->paginate(30)->withQueryString()]);
    }

    public function completeFollowUp(Request $request, FollowUp $followUp)
    {
        $data = $request->validate(['outcome' => 'nullable|string|max:255', 'note' => 'nullable|string|max:3000']);
        $followUp->update([
            'status' => 'completed',
            'completed_at' => now(),
            'outcome' => $data['outcome'] ?? null,
            'note' => $data['note'] ?? $followUp->note,
        ]);
        if ($followUp->enquiry) $followUp->enquiry->update(['next_follow_up_at' => null]);
        return back()->with('success','Follow-up completed.');
    }
}
