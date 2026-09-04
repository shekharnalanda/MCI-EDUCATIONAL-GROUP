@extends('layouts.app')

@section('title', 'News & Events | MCI Educational Group')

@section('content')

<section class="v2-page-hero">
<div class="container">
<div class="v2-kicker">News &amp; Events</div>
<h1>Updates from across the MCI Educational Group network.</h1>
<p>Follow institutional announcements, academic updates, activities and events from MCI Educational Group and its institutions.</p>
</div>
</section>

<div class="v2-breadcrumb">
<div class="container">
<a href="{{ route('home') }}">Home</a>
<span>/</span>
<span>News &amp; Events</span>
</div>
</div>

<section class="v2-section v2-soft">
<div class="container">

<div class="row align-items-end g-4 mb-5">
<div class="col-lg-7">
<div class="v2-section-kicker">Latest Updates</div>
<h2 class="v2-title display-5 mt-2 mb-0">News, announcements &amp; activities</h2>
</div>

<div class="col-lg-5">
<p class="v2-copy mb-0">Stay connected with important information and institutional activities across the MCI network.</p>
</div>
</div>

<div class="row g-4">

@forelse($items as $item)

<div class="col-md-6 col-lg-4">
<article class="v2-card p-0 overflow-hidden">

@if($item->image)
<img
 src="{{ $item->image }}"
 alt="{{ $item->title }}"
 loading="lazy"
 decoding="async"
 style="width:100%;height:225px;object-fit:cover"
>
@endif

<div class="p-4">

<div class="small text-success fw-bold">
{{ optional($item->published_at)->format('d M Y') ?: 'MCI Update' }}
</div>

<h2 class="v2-title h5 mt-2">
{{ $item->title }}
</h2>

@if($item->excerpt)
<p class="v2-copy mb-0">{{ $item->excerpt }}</p>
@endif

</div>
</article>
</div>

@empty

<div class="col-12">
<div class="alert alert-light border text-center">
No news or events have been published yet.
</div>
</div>

@endforelse

</div>
</div>
</section>

<section class="v2-section">
<div class="container">
<div class="v2-trust">

<div class="row align-items-center g-4">
<div class="col-lg-8">
<div class="text-uppercase small fw-bold opacity-75">Stay Connected</div>
<h2 class="fw-bold mt-2">Looking for institutional information?</h2>
<p class="mb-0 opacity-75">Explore our institutions or contact MCI Educational Group for admission, program and service-related enquiries.</p>
</div>

<div class="col-lg-4 text-lg-end">
<a href="{{ route('contact') }}" class="btn btn-light btn-lg fw-bold">
Contact MCI
</a>
</div>
</div>

</div>
</div>
</section>

@endsection
