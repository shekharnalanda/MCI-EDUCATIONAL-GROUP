@extends('layouts.app')

@section('title', $project['name'].' | About | MCI Educational Group')
@section('meta_description', $project['summary'])

@section('content')
<section class="v2-page-hero about-detail-hero">
<div class="container">
<div class="v2-kicker">{{ $project['category'] }} / MCI Educational Group</div>
<h1>{{ $project['name'] }}</h1>
<p>{{ $project['summary'] }}</p>
<a class="btn btn-primary btn-lg fw-bold mt-3" href="#what-we-do">Explore what we do</a>
</div>
</section>
<div class="v2-breadcrumb"><div class="container"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('about') }}#our-network">About</a><span>/</span><span>{{ $project['name'] }}</span></div></div>
<section class="v2-section" id="what-we-do"><div class="container"><div class="row g-5">
<div class="col-lg-5"><div class="v2-section-kicker">About this initiative</div><h2 class="v2-title display-5 mt-2">हम क्या करते हैं</h2><p class="v2-copy">{{ $project['summary'] }}</p></div>
<div class="col-lg-7"><div class="row g-3">
@foreach($project['services'] as $service)
<div class="col-sm-6"><div class="v2-card h-100"><div class="v2-mark">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div><h3 class="h5 fw-bold mt-3">{{ $service }}</h3></div></div>
@endforeach
</div></div></div></div></section>
<section class="v2-section v2-soft"><div class="container"><div class="v2-trust"><div class="row align-items-center g-4"><div class="col-lg-8"><div class="text-uppercase small fw-bold opacity-75">Connect with us</div><h2 class="fw-bold mt-2">MCI Educational Group</h2><p class="mb-1">Run under Chandrashekhar &amp; Narayan Educational Trust</p><p class="mb-1">MCI Campus, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar</p><p class="mb-0"><a class="text-white" href="tel:+917004773247">7004773247</a> · <a class="text-white" href="tel:+919334779133">9334779133</a> · <a class="text-white" href="mailto:mcieducationalgroup@gmail.com">mcieducationalgroup@gmail.com</a></p></div><div class="col-lg-4 text-lg-end">
@if($project['url'])<a class="btn btn-light fw-bold mb-2" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer">Visit website ↗</a><br>@endif
<a class="btn btn-outline-light fw-bold" href="{{ route('about') }}#our-network">Explore all initiatives</a></div></div></div></div></section>
@endsection
