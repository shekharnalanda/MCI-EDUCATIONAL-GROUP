@extends('layouts.app')

@section('title', 'Programs | MCI Educational Group')

@section('content')

<section class="v2-page-hero">
<div class="container">
<div class="v2-kicker">Learning &amp; Development</div>
<h1>Programs designed for education, skills and digital readiness.</h1>
<p>MCI Educational Group connects learners with academic, skill-development and technology-enabled opportunities through its institutional network.</p>
</div>
</section>

<div class="v2-breadcrumb">
<div class="container">
<a href="{{ route('home') }}">Home</a><span>/</span><span>Programs</span>
</div>
</div>

<section class="v2-section">
<div class="container">

<div class="text-center mx-auto mb-5" style="max-width:780px">
<div class="v2-section-kicker">Program Areas</div>
<h2 class="v2-title display-5 mt-2">Learning pathways for different stages and goals.</h2>
<p class="v2-copy mt-3">Specific courses and services are delivered through the relevant institutions within MCI Educational Group.</p>
</div>

<div class="row g-4">

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">01</div>
<h3 class="v2-title h5 mt-4">School Education</h3>
<p class="v2-copy mb-0">Foundational and school-level learning through the group's educational initiatives.</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">02</div>
<h3 class="v2-title h5 mt-4">Computer Education</h3>
<p class="v2-copy mb-0">Computer learning and practical digital skills for students and career development.</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">03</div>
<h3 class="v2-title h5 mt-4">Knowledge Resources</h3>
<p class="v2-copy mb-0">Learning resources and study-support environments for academic development.</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">04</div>
<h3 class="v2-title h5 mt-4">Digital &amp; Professional Services</h3>
<p class="v2-copy mb-0">Technology-enabled services supporting learners, institutions and the community.</p>
</div>
</div>

</div>
</div>
</section>

<section class="v2-section v2-soft">
<div class="container">

<div class="row g-5 align-items-center">

<div class="col-lg-6">
<div class="v2-section-kicker">How To Begin</div>
<h2 class="v2-title display-5 mt-2">Choose the opportunity that matches your requirement.</h2>
</div>

<div class="col-lg-6">
<div class="v2-card">
<h3 class="v2-title h5">Explore the institutional network</h3>
<p class="v2-copy">Programs may be offered by different institutions according to their educational or service focus.</p>

<div class="d-flex flex-wrap gap-2">
<a href="{{ route('institutions') }}" class="btn btn-primary fw-bold">
View Institutions
</a>
<a href="{{ route('contact') }}" class="btn btn-outline-primary fw-bold">
Ask MCI
</a>
</div>
</div>
</div>

</div>
</div>
</section>

<section class="v2-section">
<div class="container">
<div class="v2-trust">

<div class="row align-items-center g-4">

<div class="col-lg-8">
<div class="text-uppercase small fw-bold opacity-75">Admission &amp; Guidance</div>
<h2 class="fw-bold mt-2">Need help selecting a program?</h2>
<p class="mb-0 opacity-75">Use the central enquiry facility for guidance to the relevant institution, course or service.</p>
</div>

<div class="col-lg-4 text-lg-end">
<a href="{{ route('contact') }}" class="btn btn-light btn-lg fw-bold">
Contact MCI
</a>
</div>

</div>
</div>
</div>
</section>

@endsection
