@extends('admin.layouts.app')
@section('title','Institutions')
@section('content')
<h2 class="fw-bold mb-4">Institutions & Business Integrations</h2>
@if(session('generated_api_token'))
<div class="alert alert-warning"><strong>Copy this API token now.</strong> It will not be shown again.<br>Business code: <code>{{ session('generated_api_business') }}</code><br>Token: <code class="user-select-all">{{ session('generated_api_token') }}</code></div>
@endif
<div class="card p-4 mb-4"><form method="POST" action="{{ route('admin.institutions.store') }}">@csrf
<div class="row g-3">
<div class="col-md-4"><input class="form-control" name="name" placeholder="Institution name" required></div><div class="col-md-4"><input class="form-control" name="website_url" placeholder="https://example.com"></div><div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone"></div><div class="col-md-2"><input type="number" class="form-control" name="display_order" value="0" min="0"></div>
<div class="col-md-4"><input class="form-control" name="sender_name" placeholder="Sender name"></div><div class="col-md-4"><input class="form-control" type="email" name="sender_email" placeholder="Sender email"></div><div class="col-md-4"><input class="form-control" type="email" name="reply_to_email" placeholder="Reply-to email"></div>
<div class="col-12"><input class="form-control" name="short_description" placeholder="Short description"></div><div class="col-12"><textarea class="form-control" name="description" rows="2" placeholder="Full description"></textarea></div>
<div class="col-md-4"><input class="form-control" name="logo" placeholder="Logo URL/path"></div><div class="col-md-4"><input class="form-control" name="image" placeholder="Image URL/path"></div>
<div class="col-md-4 d-flex gap-3 align-items-center"><label><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Active</label><label><input class="form-check-input" type="checkbox" name="sync_enabled" value="1" checked> Central Sync</label><label><input class="form-check-input" type="checkbox" name="auto_reply_enabled" value="1"> Auto Reply</label></div>
<div class="col-12"><button class="btn btn-primary">Add Institution</button></div></div></form></div>

@foreach($items as $item)
<div class="card p-3 mb-3">
<div class="d-flex justify-content-between align-items-center mb-2"><div><strong>{{ $item->name }}</strong> <code>{{ $item->slug }}</code></div><span class="badge {{ $item->api_token_hash ? 'text-bg-success' : 'text-bg-secondary' }}">API {{ $item->api_token_hash ? 'Configured' : 'Not Configured' }}</span></div>
<form method="POST" action="{{ route('admin.institutions.update',$item) }}">@csrf @method('PUT')
<div class="row g-2"><div class="col-md-3"><input class="form-control" name="name" value="{{ $item->name }}" required></div><div class="col-md-3"><input class="form-control" name="website_url" value="{{ $item->website_url }}"></div><div class="col-md-2"><input class="form-control" name="phone" value="{{ $item->phone }}" placeholder="Phone"></div><div class="col-md-2"><input type="number" class="form-control" name="display_order" value="{{ $item->display_order }}" min="0"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Update</button></div>
<div class="col-md-4"><input class="form-control" name="sender_name" value="{{ $item->sender_name }}" placeholder="Sender name"></div><div class="col-md-4"><input class="form-control" type="email" name="sender_email" value="{{ $item->sender_email }}" placeholder="Sender email"></div><div class="col-md-4"><input class="form-control" type="email" name="reply_to_email" value="{{ $item->reply_to_email }}" placeholder="Reply-to email"></div>
<div class="col-12"><input class="form-control" name="short_description" value="{{ $item->short_description }}"></div><div class="col-12"><textarea class="form-control" name="description" rows="2">{{ $item->description }}</textarea></div><div class="col-md-4"><input class="form-control" name="logo" value="{{ $item->logo }}"></div><div class="col-md-4"><input class="form-control" name="image" value="{{ $item->image }}"></div>
<div class="col-md-4 d-flex gap-3 align-items-center"><label><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label><label><input type="checkbox" name="sync_enabled" value="1" @checked($item->sync_enabled)> Central Sync</label><label><input type="checkbox" name="auto_reply_enabled" value="1" @checked($item->auto_reply_enabled)> Auto Reply</label></div></div></form>
<div class="d-flex gap-2 mt-3"><form method="POST" action="{{ route('admin.institutions.api-token',$item) }}">@csrf<button class="btn btn-sm btn-outline-dark" onclick="return confirm('Generate a new token? The old token will stop working.')">{{ $item->api_token_hash ? 'Regenerate API Token' : 'Generate API Token' }}</button></form><form method="POST" action="{{ route('admin.institutions.destroy',$item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this institution?')">Delete</button></form></div>
</div>
@endforeach
@endsection
