@extends('layouts.app')

@section('title', 'Downloads | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom">
    <div class="container py-4">
        <span class="badge bg-primary-subtle text-primary mb-3">Resources</span>
        <h1 class="display-5 fw-bold">Downloads</h1>
        <p class="lead text-secondary mb-0">Important forms, brochures, notices and learning resources published for students and visitors.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 d-flex flex-column">
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
