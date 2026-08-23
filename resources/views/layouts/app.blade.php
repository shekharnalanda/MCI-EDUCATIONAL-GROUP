<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MCI Educational Group')</title>
    <meta name="description" content="@yield('meta_description', 'MCI Educational Group - An Institution With Global Reach')">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--mci-blue:#0d4fa3;--mci-green:#1aa260;--mci-dark:#0d2540;--mci-light:#f4f8fc}
        body{font-family:Arial,Helvetica,sans-serif;color:#203047;background:#fff}
        .topbar{background:var(--mci-dark);color:#fff;font-size:.9rem}
        .topbar a{color:#fff;text-decoration:none}
        .navbar-brand{font-weight:800;color:var(--mci-blue)!important}
        .navbar .nav-link{font-weight:600;color:#21364f}
        .navbar .nav-link:hover{color:var(--mci-green)}
        .page-hero{background:linear-gradient(120deg,rgba(13,79,163,.96),rgba(26,162,96,.88));color:#fff;padding:80px 0}
        .section-title{font-weight:800;color:var(--mci-dark)}
        .institution-card{height:100%;border:0;border-radius:18px;box-shadow:0 12px 30px rgba(18,54,92,.10);transition:.2s}
        .institution-card:hover{transform:translateY(-4px)}
        .btn-mci{background:linear-gradient(90deg,var(--mci-blue),var(--mci-green));color:#fff;border:0}
        .btn-mci:hover{color:#fff;opacity:.95}
        footer{background:#0d2540;color:#d9e4ef}
        footer a{color:#d9e4ef;text-decoration:none}
    </style>
    @stack('styles')
</head>
<body>
<div class="topbar py-2">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <div>Work under Chandrashekhar &amp; Narayan Educational Trust</div>
        <div><a href="tel:+917004773247">7004773247</a> · <a href="tel:+919334779133">9334779133</a> · <a href="mailto:mcieducationalgroup@gmail.com">mcieducationalgroup@gmail.com</a></div>
    </div>
</div>
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">MCI EDUCATIONAL GROUP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mciNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mciNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('institutions') }}">Institutions</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('programs') }}">Programs</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('news-events') }}">News & Events</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('gallery') }}">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<main>@yield('content')</main>

<footer class="pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h5 class="text-white">MCI Educational Group</h5>
                <p>MCI CAMPUS, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar (India)</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Quick Links</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('institutions') }}">Our Institutions</a>
                    <a href="{{ route('programs') }}">Programs</a>
                    <a href="{{ route('downloads') }}">Downloads</a>
                    <a href="{{ route('career') }}">Career</a>
                </div>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Contact</h6>
                <p class="mb-1">7004773247, 9334779133</p>
                <p class="mb-0">mcieducationalgroup@gmail.com</p>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="small">© {{ date('Y') }} MCI Educational Group. All rights reserved.</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
