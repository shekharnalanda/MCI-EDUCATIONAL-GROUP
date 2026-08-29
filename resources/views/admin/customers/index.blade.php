@extends('admin.layouts.app')
@section('title','Customers')
@section('content')
<h2 class="fw-bold mb-4">Customers</h2><div class="card p-3 mb-4"><form class="row g-2"><div class="col-md-10"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name, mobile or email"></div><div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div></form></div>
@forelse($items as $item)<div class="card p-3 mb-3"><div class="row align-items-center"><div class="col-md-4"><strong>{{ $item->name ?: 'Unnamed Customer' }}</strong><br><small>{{ $item->mobile }} {{ $item->email }}</small></div><div class="col-md-2">Enquiries: <strong>{{ $item->enquiries_count }}</strong></div><div class="col-md-2">Admissions: <strong>{{ $item->admissions_count }}</strong></div><div class="col-md-2"><small>Last: {{ optional($item->last_activity_at)->format('d M Y H:i') }}</small></div><div class="col-md-2"><a class="btn btn-outline-primary w-100" href="{{ route('admin.customers.show',$item) }}">History</a></div></div></div>@empty<div class="alert alert-info">No customers found.</div>@endforelse
{{ $items->links() }}
@endsection
