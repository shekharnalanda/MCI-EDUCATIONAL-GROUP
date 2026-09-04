@extends('layouts.app')

@section('title', 'Gallery | MCI Educational Group')

@section('content')

<section class="v2-page-hero">
<div class="container">
<div class="v2-kicker">Campus &amp; Activities</div>
<h1>Moments from learning, training and institutional life.</h1>
<p>Explore photos and highlights from classrooms, programs, events and activities across MCI Educational Group.</p>
</div>
</section>

<div class="v2-breadcrumb">
<div class="container">
<a href="{{ route('home') }}">Home</a>
<span>/</span>
<span>Gallery</span>
</div>
</div>

<section class="v2-section">
<div class="container">

<div class="text-center mx-auto mb-5" style="max-width:760px">
<div class="v2-section-kicker">Gallery Highlights</div>
<h2 class="v2-title display-5 mt-2">Learning and activities in focus.</h2>
<p class="v2-copy mt-3">A visual glimpse into the educational and institutional activities of the MCI network.</p>
</div>

<div class="row g-4">

@forelse($items as $item)

<div class="col-md-6 col-lg-4">

<figure class="v2-card p-0 overflow-hidden mb-0">

<img
 src="{{ $item->image }}"
 alt="{{ $item->title }}"
 loading="lazy"
 decoding="async"
 style="width:100%;height:260px;object-fit:cover"
>

<figcaption class="p-4">

<h2 class="v2-title h6 mb-2">
{{ $item->title }}
</h2>

@if($item->caption)
<p class="v2-copy mb-0">
{{ $item->caption }}
</p>
@endif

</figcaption>

</figure>
</div>

@empty

<div class="col-12">
<div class="alert alert-light border text-center">
Gallery photos will be published soon.
</div>
</div>

@endforelse

</div>
</div>
</section>

@endsection
