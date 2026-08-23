@extends('layouts.app')

@section('title', 'Gallery | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom">
    <div class="container py-4">
        <span class="badge bg-success-subtle text-success mb-3">Campus Life</span>
        <h1 class="display-5 fw-bold">Gallery</h1>
        <p class="lead text-secondary mb-0">Photos and highlights from classrooms, events, training programs and activities across MCI Educational Group.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($gallery as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <img src="{{ $item->image }}" alt="{{ $item->title }}" class="card-img-top" style="height:240px;object-fit:cover">
                        <div class="card-body">
                            <h2 class="h6 fw-bold">{{ $item->title }}</h2>
                            @if($item->caption)<p class="text-secondary mb-0">{{ $item->caption }}</p>@endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light border text-center">Gallery photos will be published soon.</div></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
