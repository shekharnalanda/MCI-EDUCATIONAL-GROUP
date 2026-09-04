@extends('layouts.app')

@section('title', 'Our Institutions | MCI Educational Group')

@section('content')

<section class="v2-page-hero">
<div class="container">
<div class="v2-kicker">MCI Institutional Network</div>
<h1>One group. Multiple institutions and learning opportunities.</h1>
<p>Explore the education, training, knowledge and professional service initiatives connected through MCI Educational Group.</p>
</div>
</section>

<div class="v2-breadcrumb">
<div class="container">
<a href="{{ route('home') }}">Home</a><span>/</span><span>Institutions</span>
</div>
</div>

<section class="v2-section v2-soft">
<div class="container">

<div class="row align-items-end g-4 mb-5">
<div class="col-lg-7">
<div class="v2-section-kicker">Our Network</div>
<h2 class="v2-title display-5 mt-2 mb-0">Institutions &amp; Services</h2>
</div>

<div class="col-lg-5">
<p class="v2-copy mb-0">Each unit retains its own focus while remaining connected through the broader MCI Educational Group ecosystem.</p>
</div>
</div>

<div class="row g-4">

@forelse($institutions as $item)

@php
$words = preg_split('/\s+/', trim($item->name));
$code = strtoupper(
    collect($words)->filter()->map(fn($word) => substr($word,0,1))->implode('')
);
@endphp

<div class="col-md-6 col-xl-4">
<article class="institution-card">

<div class="institution-accent"></div>

<div class="institution-body">

<div class="institution-logo-wrap">

@if($item->logo || $item->image)

<img
 src="{{ $item->logo ?: $item->image }}"
 alt="{{ $item->name }} logo"
 class="institution-logo"
 loading="lazy"
 decoding="async"
 onerror="this.style.display='none';this.nextElementSibling.style.display='grid';"
>

<span style="display:none;width:100%;height:100%;place-items:center;font-weight:900;color:#0866b0">
{{ $code }}
</span>

@else

<span style="display:grid;width:100%;height:100%;place-items:center;font-weight:900;color:#0866b0">
{{ $code }}
</span>

@endif

</div>

<h2 class="v2-title h4 mt-4">{{ $item->name }}</h2>

<p class="v2-copy">
{{ $item->short_description ?: $item->description }}
</p>

@if($item->website_url)
<a
 href="{{ $item->website_url }}"
 target="_blank"
 rel="noopener"
 class="btn btn-outline-primary fw-bold">
Visit Official Website
</a>
@endif

</div>
</article>
</div>

@empty

<div class="col-12">
<div class="alert alert-light border text-center">
Institution information will be published soon.
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
<div class="text-uppercase small fw-bold opacity-75">Central Enquiry</div>
<h2 class="fw-bold mt-2">Not sure which institution is right for you?</h2>
<p class="mb-0 opacity-75">Send your requirement through the MCI central enquiry system and connect with the appropriate institution or service.</p>
</div>

<div class="col-lg-4 text-lg-end">
<a href="{{ route('contact') }}" class="btn btn-light btn-lg fw-bold">
Send Enquiry
</a>
</div>

</div>
</div>

</div>
</section>

@endsection
