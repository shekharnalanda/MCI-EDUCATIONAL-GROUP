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
            <p class="text-muted mb-0">More institutions and projects can be added in future through the website administration system.</p>
        </div>
        <div class="row g-4">
            @php
                $institutions = [
                    ['name'=>'Micro Computer Institute','url'=>'https://mciedu.com','text'=>'Computer education, skills and training programs.'],
                    ['name'=>'C-Net Computer Institute','url'=>'https://cnetcomputer.mciedu.com','text'=>'Computer education and career-oriented learning.'],
                    ['name'=>'C-Net Pathshala','url'=>'https://c-net.mciedu.in','text'=>'Digital and academic learning platform for students.'],
                    ['name'=>'C-Net Library','url'=>'https://cnetlibrary.mciedu.com','text'=>'Library, study and knowledge services for learners.'],
                    ['name'=>'C-Net Web Services','url'=>'https://web.mciedu.in','text'=>'Website, digital presence and related web services.'],
                ];
            @endphp
            @foreach($institutions as $institution)
                <div class="col-md-6 col-lg-4">
                    <div class="card institution-card p-4">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3"><span class="badge rounded-pill text-bg-light border">MCI Group</span></div>
                            <h4 class="section-title">{{ $institution['name'] }}</h4>
                            <p class="text-muted flex-grow-1">{{ $institution['text'] }}</p>
                            <a href="{{ $institution['url'] }}" target="_blank" rel="noopener" class="btn btn-mci rounded-pill align-self-start">Visit Website</a>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-md-6 col-lg-4">
                <div class="card institution-card p-4 border border-2 border-dashed">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h4 class="section-title">Future Institution</h4>
                        <p class="text-muted mb-0">This structure is designed so additional institutions can be added without redesigning the website.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
