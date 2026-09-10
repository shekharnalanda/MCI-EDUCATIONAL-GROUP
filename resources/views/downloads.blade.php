@extends('layouts.app')

@section('title', 'Downloads | MCI Educational Group')

@section('content')
<section class="v2-page-hero"><div class="container"><div class="v2-kicker">Resources &amp; Documents</div><h1>Downloads for students, institutions and visitors.</h1><p>Access important forms, brochures, notices and learning resources published by MCI Educational Group.</p></div></section>
<div class="v2-breadcrumb"><div class="container"><a href="{{ route('home') }}">Home</a><span>/</span><span>Downloads</span></div></div>
<section class="v2-section v2-soft">
    <div class="container">
        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-md-6">
                    <div class="v2-card"><div class="d-flex flex-column h-100">
                            <h2 class="h5 fw-bold">{{ $item->title }}</h2>
                            @if($item->description)<p class="text-secondary flex-grow-1">{{ $item->description }}</p>@endif
                            <div class="d-flex gap-2 flex-wrap mt-2">
                                @if($item->file_path)
                                    <a class="btn btn-primary" href="{{ asset($item->file_path) }}" target="_blank" rel="noopener">Download File</a>
                                @endif
                                @if($item->external_url)
                                    <a class="btn btn-outline-primary" href="{{ $item->external_url }}" target="_blank" rel="noopener">Open Link</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light border text-center">No downloads have been published yet.</div></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
