@extends('admin.layouts.app')
@section('title','Central Enquiries')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h2 class="fw-bold mb-1">Central Enquiries</h2><p class="text-muted mb-0">Filter and handle enquiries from all MCI Educational Group units.</p></div>
</div>
<form method="GET" class="card p-3 mb-4"><div class="row g-2">
    <div class="col-md-3"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, phone, email or message"></div>
    <div class="col-md-2"><select name="institution_id" class="form-select"><option value="">All Businesses</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}" @selected((string)request('institution_id')===(string)$institution->id)>{{ $institution->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><select name="status" class="form-select"><option value="">All Statuses</option>@foreach(['new','auto_replied','manual_review','replied','follow_up_due','in_progress','converted','admitted','no_response','closed'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endforeach</select></div>
    <div class="col-md-2"><select name="auto_reply_status" class="form-select"><option value="">Auto Reply</option>@foreach(['pending','sent','manual_review','failed','not_applicable'] as $status)<option value="{{ $status }}" @selected(request('auto_reply_status')===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endforeach</select></div>
    <div class="col-md-2"><select name="category" class="form-select"><option value="">All Categories</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category')===$category)>{{ $category }}</option>@endforeach</select></div>
    <div class="col-md-1 d-grid"><button class="btn btn-primary">Filter</button></div>
</div></form>
<div class="card p-0 overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0">
<thead class="table-light"><tr><th>Customer</th><th>Business</th><th>Category</th><th>Status</th><th>Auto Reply</th><th>Received</th><th></th></tr></thead>
<tbody>@forelse($items as $item)<tr>
<td><strong>{{ $item->name }}</strong><br><small class="text-muted">{{ $item->phone }} @if($item->email) · {{ $item->email }} @endif</small><br><small>{{ \Illuminate\Support\Str::limit($item->message,80) }}</small></td>
<td>{{ $item->institution?->name ?? 'MCI Educational Group' }}<br><small class="text-muted">{{ $item->source_site ?: 'mciedu.in' }}</small></td>
<td>{{ $item->category ?: '-' }}<br><small class="text-muted">{{ $item->course_service ?: '' }}</small></td>
<td><span class="badge text-bg-secondary">{{ ucwords(str_replace('_',' ',$item->status)) }}</span></td>
<td>{{ ucwords(str_replace('_',' ',$item->auto_reply_status)) }}</td>
<td>{{ ($item->received_at ?: $item->created_at)?->format('d M Y, h:i A') }}</td>
<td><a href="{{ route('admin.enquiries.show',$item) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
</tr>@empty<tr><td colspan="7" class="text-center py-5 text-muted">No enquiries found.</td></tr>@endforelse</tbody>
</table></div></div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
