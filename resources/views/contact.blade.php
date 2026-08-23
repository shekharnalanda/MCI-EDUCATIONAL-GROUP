@extends('layouts.app')

@section('title', 'Contact Us | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom">
    <div class="container py-4">
        <span class="badge bg-primary-subtle text-primary mb-3">Get in Touch</span>
        <h1 class="display-5 fw-bold">Contact MCI Educational Group</h1>
        <p class="lead text-secondary mb-0">Reach us for institution information, admissions, partnerships, services and general enquiries.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 fw-bold mb-4">Contact Details</h2>
                        <p><strong>Address</strong><br>MCI CAMPUS, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar, India</p>
                        <p><strong>Phone</strong><br>7004773247<br>9334779133</p>
                        <p class="mb-0"><strong>Email</strong><br>mcieducationalgroup@gmail.com</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 fw-bold">Send an Enquiry</h2>
                        <p class="text-secondary">Your message will be available directly in the MCI Admin Panel.</p>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form method="POST" action="{{ route('enquiry.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name *</label>
                                    <input class="form-control" name="name" value="{{ old('name') }}" maxlength="120" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone *</label>
                                    <input class="form-control" name="phone" value="{{ old('phone') }}" maxlength="30" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" maxlength="150">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject</label>
                                    <input class="form-control" name="subject" value="{{ old('subject') }}" maxlength="180">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message *</label>
                                    <textarea class="form-control" name="message" rows="5" maxlength="3000" required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary px-4">Submit Enquiry</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
