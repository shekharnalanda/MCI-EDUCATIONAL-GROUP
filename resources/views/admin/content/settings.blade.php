@extends('admin.layouts.app')
@section('title','Settings')
@section('content')
<h2 class="fw-bold mb-4">Website Settings</h2>
<div class="card p-4"><form method="POST" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
@forelse($items as $item)<div class="mb-3"><label class="form-label fw-semibold">{{ ucwords(str_replace('_',' ',$item->key)) }}</label><input class="form-control" name="settings[{{ $item->key }}]" value="{{ $item->value }}"></div>@empty<div class="alert alert-info">No settings found.</div>@endforelse
<button class="btn btn-primary">Save Settings</button></form></div>
@endsection
