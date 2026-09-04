@extends('layouts.app')

@section('title', 'Contact Us | MCI Educational Group')

@section('content')

<section class="v2-page-hero">
<div class="container">
<div class="v2-kicker">Contact &amp; Enquiry</div>
<h1>Connect with MCI Educational Group.</h1>
<p>Reach us for institution information, admissions, programs, partnerships, services and general enquiries.</p>
</div>
</section>

<div class="v2-breadcrumb">
<div class="container">
<a href="{{ route('home') }}">Home</a>
<span>/</span>
<span>Contact</span>
</div>
</div>

<section class="v2-section v2-soft">
<div class="container">

<div class="row g-4 g-lg-5">

<div class="col-lg-5">

<div class="v2-card">

<div class="v2-section-kicker">Contact Details</div>

<h2 class="v2-title h3 mt-2 mb-4">
MCI Educational Group
</h2>

<div class="mb-4">
<div class="small text-uppercase fw-bold text-success mb-1">Address</div>
<div class="v2-copy">
MCI CAMPUS, Quamruddin Ganj,<br>
Bihar Sharif, Nalanda - 803101,<br>
Bihar, India
</div>
</div>

<div class="mb-4">
<div class="small text-uppercase fw-bold text-success mb-1">Phone</div>
<div class="v2-copy">
7004773247<br>
9334779133
</div>
</div>

<div>
<div class="small text-uppercase fw-bold text-success mb-1">Email</div>
<a href="mailto:mcieducationalgroup@gmail.com">
mcieducationalgroup@gmail.com
</a>
</div>

<hr class="my-4">

<p class="v2-copy mb-0">
Your enquiry can be directed to the relevant institution or service within the MCI Educational Group network.
</p>

</div>
</div>

<div class="col-lg-7">

<div class="v2-card">

<div class="v2-section-kicker">Central Enquiry</div>

<h2 class="v2-title h3 mt-2">
Send an Enquiry
</h2>

<p class="v2-copy">
Your message will be available directly in the MCI Admin Panel.
</p>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
{{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('enquiry.store') }}">
@csrf

<div class="row g-3">

<div class="col-md-6">
<label class="form-label fw-bold" for="name">Name *</label>
<input
 id="name"
 class="form-control form-control-lg"
 name="name"
 value="{{ old('name') }}"
 maxlength="120"
 autocomplete="name"
 required
>
</div>

<div class="col-md-6">
<label class="form-label fw-bold" for="phone">Phone *</label>
<input
 id="phone"
 class="form-control form-control-lg"
 name="phone"
 value="{{ old('phone') }}"
 maxlength="30"
 autocomplete="tel"
 required
>
</div>

<div class="col-12">
<label class="form-label fw-bold" for="email">Email</label>
<input
 id="email"
 type="email"
 class="form-control form-control-lg"
 name="email"
 value="{{ old('email') }}"
 maxlength="150"
 autocomplete="email"
>
</div>

<div class="col-12">
<label class="form-label fw-bold" for="subject">Subject</label>
<input
 id="subject"
 class="form-control form-control-lg"
 name="subject"
 value="{{ old('subject') }}"
 maxlength="180"
>
</div>

<div class="col-12">
<label class="form-label fw-bold" for="message">Message *</label>
<textarea
 id="message"
 class="form-control"
 name="message"
 rows="6"
 maxlength="3000"
 required>{{ old('message') }}</textarea>
</div>

<div class="col-12 pt-2">
<button type="submit" class="btn btn-primary btn-lg px-4 fw-bold">
Submit Enquiry
</button>
</div>

</div>
</form>

</div>
</div>

</div>
</div>
</section>

@endsection
