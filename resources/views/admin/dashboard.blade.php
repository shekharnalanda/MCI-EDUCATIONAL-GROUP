@extends('admin.layouts.app')
@section('title','Master Dashboard')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h2 class="fw-bold mb-1">MCI Master Dashboard</h2><p class="text-muted mb-0">Central enquiry and group administration overview.</p></div>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary">View Website</a>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total Enquiries',$enquiryCount],
        ["Today's Enquiries",$todayEnquiryCount],
        ['Pending Replies',$pendingReplyCount],
        ['Auto Replied',$autoRepliedCount],
        ['Manual Review',$manualReviewCount],
        ['Follow-up Due',$followUpDueCount],
    ] as [$label,$count])
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card p-3 h-100">
                <div class="text-muted small text-uppercase fw-semibold">{{ $label }}</div>
                <div class="display-6 fw-bold mt-2">{{ $count }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    @foreach([
        ['Institutions / Businesses',$institutionCount,'admin.institutions.index'],
        ['Central Enquiries',$enquiryCount,'admin.enquiries.index'],
        ['News & Events',$newsCount,'admin.news.index'],
        ['Gallery',$galleryCount,'admin.gallery.index'],
        ['Downloads',$downloadCount,'admin.downloads.index'],
    ] as [$label,$count,$route])
        <div class="col-sm-6 col-xl-4"><div class="card p-4 h-100"><div class="text-muted small text-uppercase fw-semibold">{{ $label }}</div><div class="display-5 fw-bold my-2">{{ $count }}</div><a href="{{ route($route) }}" class="text-decoration-none">Manage {{ $label }} →</a></div></div>
    @endforeach
</div>
@endsection
