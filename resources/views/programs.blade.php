@extends('layouts.app')

@section('title', 'Programs & Services | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom">
    <div class="container py-4">
        <span class="badge bg-success-subtle text-success mb-3">Learning & Growth</span>
        <h1 class="display-5 fw-bold">Programs & Educational Services</h1>
        <p class="lead text-secondary mb-0">Explore education, skill development, digital learning, career support and technology services offered across the MCI Educational Group network.</p>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['Computer Education','Certificate, diploma and practical computer education through our institutes.'],
                ['Online Learning','Digital study support, learning resources and student-focused online services.'],
                ['Library & Study Support','Reading, study hall and knowledge resources for students and competitive learners.'],
                ['Web & Digital Services','Website development, digital presence and technology support through C-Net Web Services.'],
                ['Career & Skill Development','Skill-oriented learning, job information and employability-focused guidance.'],
                ['Future Programs','New educational and service programs can be added from the admin panel as the group expands.']
            ] as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-bold">{{ $item[0] }}</h3>
                        <p class="text-secondary mb-0">{{ $item[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
