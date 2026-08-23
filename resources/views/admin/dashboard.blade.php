@extends('admin.layouts.app')
@section('title','Dashboard')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h2 class="fw-bold mb-1">Dashboard</h2><p class="text-muted mb-0">MCI Educational Group administration overview.</p></div>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary">View Website</a>
</div>
<div class="row g-4">
    @foreach([
        ['Institutions',$institutionCount,'admin.institutions.index'],
        ['News & Events',$newsCount,'admin.news.index'],
        ['Gallery',$galleryCount,'admin.gallery.index'],
        ['Downloads',$downloadCount,'admin.downloads.index'],
        ['Enquiries',$enquiryCount,'admin.enquiries.index'],
    ] as [$label,$count,$route])
        <div class="col-sm-6 col-xl-4"><div class="card p-4 h-100"><div class="text-muted small text-uppercase fw-semibold">{{ $label }}</div><div class="display-5 fw-bold my-2">{{ $count }}</div><a href="{{ route($route) }}" class="text-decoration-none">Manage {{ $label }} →</a></div></div>
    @endforeach
</div>
@endsection
