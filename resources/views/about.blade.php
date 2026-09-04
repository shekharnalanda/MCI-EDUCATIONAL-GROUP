@extends('layouts.app')

@section('title', 'About Us | MCI Educational Group')

@section('content')

<section class="v2-page-hero">
<div class="container">
<div class="v2-kicker">About MCI Educational Group</div>

<h1>Education, skills and opportunities under one institutional ecosystem.</h1>

<p>
MCI Educational Group brings together education, training,
digital learning and professional services through a coordinated
institutional framework.
</p>
</div>
</section>

<div class="v2-breadcrumb">
<div class="container">
<a href="{{ route('home') }}">Home</a>
<span>/</span>
<span>About</span>
</div>
</div>

<section class="v2-section">
<div class="container">

<div class="row g-5 align-items-center">

<div class="col-lg-6">
<div class="v2-section-kicker">Who We Are</div>

<h2 class="v2-title display-5 mt-2">
Building accessible and future-ready learning opportunities.
</h2>
</div>

<div class="col-lg-6">
<p class="v2-copy">
MCI Educational Group serves as a common institutional identity
for multiple education, training, knowledge and service initiatives.
Our aim is to provide accessible, practical and future-ready learning
opportunities while creating a common platform for students,
families, institutions and the community.
</p>

<p class="v2-copy mb-0">
The group continues to strengthen its academic, digital and
administrative systems so that each institution can develop
within a coordinated and professionally managed ecosystem.
</p>
</div>

</div>
</div>
</section>

<section class="v2-section v2-soft">
<div class="container">

<div class="text-center mx-auto mb-5" style="max-width:760px">
<div class="v2-section-kicker">Our Institutional Approach</div>

<h2 class="v2-title display-5 mt-2">
Learning with purpose. Growth with responsibility.
</h2>
</div>

<div class="row g-4">

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">01</div>
<h3 class="v2-title h5 mt-4">Education</h3>
<p class="v2-copy mb-0">
Learner-focused academic and foundational opportunities.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">02</div>
<h3 class="v2-title h5 mt-4">Skills</h3>
<p class="v2-copy mb-0">
Practical learning designed to support career readiness.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">03</div>
<h3 class="v2-title h5 mt-4">Digital Access</h3>
<p class="v2-copy mb-0">
Technology-enabled platforms connecting learners and services.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="v2-card">
<div class="v2-mark">04</div>
<h3 class="v2-title h5 mt-4">Growth</h3>
<p class="v2-copy mb-0">
A scalable framework supporting future institutions and initiatives.
</p>
</div>
</div>

</div>
</div>
</section>

<section class="v2-section">
<div class="container">

<div class="v2-trust">

<div class="row g-4 align-items-center">

<div class="col-lg-8">
<div class="text-uppercase small fw-bold opacity-75">
Trust &amp; Governance
</div>

<h2 class="fw-bold mt-2">
Chandrashekhar &amp; Narayan Educational Trust
</h2>

<p class="mb-0 opacity-75">
MCI Educational Group operates within a common institutional
framework supporting coordinated administration, continuity
and responsible educational development.
</p>
</div>

<div class="col-lg-4 text-lg-end">
<a href="{{ route('institutions') }}"
class="btn btn-light btn-lg fw-bold">
Explore Our Institutions
</a>
</div>

</div>
</div>

</div>
</section>

@endsection
