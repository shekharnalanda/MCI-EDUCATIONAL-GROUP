@extends('admin.layouts.app')
@section('title','Enquiries')
@section('content')
<h2 class="fw-bold mb-4">Enquiries</h2>
@forelse($items as $item)<div class="card p-3 mb-3"><div class="row g-2"><div class="col-md-3"><strong>{{ $item->name }}</strong><br><small>{{ $item->phone }} {{ $item->email }}</small></div><div class="col-md-5"><div>{{ $item->subject }}</div><small class="text-muted">{{ $item->message }}</small></div><div class="col-md-2"><form method="POST" action="{{ route('admin.enquiries.update',$item) }}">@csrf @method('PATCH')<select name="status" class="form-select" onchange="this.form.submit()"><option value="new" @selected($item->status==='new')>New</option><option value="contacted" @selected($item->status==='contacted')>Contacted</option><option value="closed" @selected($item->status==='closed')>Closed</option></select></form></div><div class="col-md-2"><form method="POST" action="{{ route('admin.enquiries.destroy',$item) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger w-100" onclick="return confirm('Delete enquiry?')">Delete</button></form></div></div></div>@empty<div class="alert alert-info">No enquiries yet.</div>@endforelse
@endsection
