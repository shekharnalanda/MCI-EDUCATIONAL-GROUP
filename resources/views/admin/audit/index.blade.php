@extends('admin.layouts.app')
@section('title','Audit Logs')
@section('content')
<h2 class="fw-bold mb-4">Audit Logs</h2>
<form class="card p-3 mb-4"><div class="row g-2"><div class="col-md-4"><input class="form-control" name="action" value="{{ request('action') }}" placeholder="Filter action"></div><div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div></div></form>
<div class="card p-3"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date</th><th>User</th><th>Business</th><th>Action</th><th>Record</th><th>IP</th></tr></thead><tbody>@forelse($items as $item)<tr><td>{{ $item->created_at }}</td><td>{{ $item->user?->name ?? '-' }}</td><td>{{ $item->institution?->name ?? '-' }}</td><td><code>{{ $item->action }}</code></td><td>{{ class_basename($item->auditable_type ?: '-') }} #{{ $item->auditable_id }}</td><td>{{ $item->ip_address }}</td></tr>@empty<tr><td colspan="6" class="text-muted text-center">No audit records.</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}</div>
@endsection
