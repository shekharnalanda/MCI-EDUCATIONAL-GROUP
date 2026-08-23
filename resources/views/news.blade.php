@extends('layouts.app')

@section('title', 'News & Events | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom">
    <div class="container py-4">
        <span class="badge bg-primary-subtle text-primary mb-3">Updates</span>
        <h1 class="display-5 fw-bold">News & Events</h1>
        <p class="lead text-secondary mb-0">Latest announcements, academic updates, activities and events from MCI Educational Group and its institutions.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($news as $item)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        @if($item->image)
                            <img src="{{ $item->image }}" alt="{{ $item->title }}" class="card-img-top" style="height:220px;object-fit:cover">
                        @endif
                        <div class="card-body p-4">
                            <span class="small text-success fw-semibold">{{ optional($item->published_at)->format('d M Y') ?: 'MCI Update' }}</span>
                            <h2 class="h5 fw-bold mt-2">{{ $item->title }}</h2>
                            @if($item->excerpt)<p class="text-secondary mb-0">{{ $item->excerpt }}</p>@endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light border text-center">No news or events have been published yet.</div></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
