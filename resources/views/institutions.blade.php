@extends('layouts.app')

@section('title','Our Institutions | MCI Educational Group')
@section('meta_description','Explore the institutions and projects of MCI Educational Group.')

@section('content')
<section class="page-hero">
    <div class="container">
        <span class="badge bg-light text-primary mb-3">Our Network</span>
        <h1 class="display-5 fw-bold">Our Institutions & Projects</h1>
        <p class="lead mb-0">A growing educational and digital ecosystem under MCI Educational Group.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Current Institutions</h2>
            <p class="text-muted mb-0">More institutions and projects can be added any time from the admin panel.</p>
        </div>
        <div class="row g-4">
            @forelse($institutions as $institution)
                <div class="col-md-6 col-lg-4">
                    <div class="card institution-card p-4 h-100">
                        <div class="card-body d-flex flex-column">
                            @if($institution->logo)
                                <img src="{{ $institution->logo }}" alt="{{ $institution->name }} logo" class="mb-3" style="max-height:72px;max-width:160px;object-fit:contain">
                            @endif
                            <div class="mb-3"><span class="badge rounded-pill text-bg-light border">MCI Group</span></div>
                            <h4 class="section-title">{{ $institution->name }}</h4>
                            <p class="text-muted flex-grow-1">{{ $institution->short_description ?: $institution->description }}</p>
                            @if($institution->website_url)
                                <a href="{{ $institution->website_url }}" target="_blank" rel="noopener" class="btn btn-mci rounded-pill align-self-start">Visit Website</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light border text-center">Institution information will be published soon.</div></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
