<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seoTitle = trim($__env->yieldContent('title', 'MCI Educational Group'));
        $seoDescription = trim($__env->yieldContent('meta_description', 'MCI Educational Group - An Institution With Global Reach'));
        $canonicalUrl = url()->current();
        $logoUrl = asset('images/mci-logo.png');
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">
    <meta property="og:type" content="website"><meta property="og:site_name" content="MCI Educational Group"><meta property="og:title" content="{{ $seoTitle }}"><meta property="og:description" content="{{ $seoDescription }}"><meta property="og:url" content="{{ $canonicalUrl }}"><meta property="og:image" content="{{ $logoUrl }}">
    <meta name="twitter:card" content="summary"><meta name="twitter:title" content="{{ $seoTitle }}"><meta name="twitter:description" content="{{ $seoDescription }}"><meta name="twitter:image" content="{{ $logoUrl }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--mci-blue:#0d4fa3;--mci-green:#1aa260;--mci-dark:#0d2540;--mci-light:#f4f8fc}body{font-family:Arial,Helvetica,sans-serif;color:#203047;background:#fff}.topbar{background:var(--mci-dark);color:#fff;font-size:.9rem}.topbar a{color:#fff;text-decoration:none}.navbar-brand{font-weight:800;color:var(--mci-blue)!important}.brand-logo{width:58px;height:58px;object-fit:contain;flex:0 0 58px}.brand-copy{line-height:1.08}.brand-copy small{display:block;font-size:.72rem;font-weight:600;color:#607080;margin-top:4px}.footer-logo{width:82px;height:82px;object-fit:contain;background:#fff;border-radius:16px;padding:5px;margin-bottom:14px}.navbar .nav-link{font-weight:600;color:#21364f}.navbar .nav-link:hover{color:var(--mci-green)}.admin-login-btn{background:linear-gradient(90deg,var(--mci-blue),var(--mci-green));color:#fff!important;border-radius:999px;padding:.5rem .9rem!important;margin-left:.4rem}.page-hero{background:linear-gradient(120deg,rgba(13,79,163,.96),rgba(26,162,96,.88));color:#fff;padding:80px 0}.section-title{font-weight:800;color:var(--mci-dark)}.institution-card{height:100%;border:0;border-radius:18px;box-shadow:0 12px 30px rgba(18,54,92,.10);transition:.2s}.institution-card:hover{transform:translateY(-4px)}.btn-mci{background:linear-gradient(90deg,var(--mci-blue),var(--mci-green));color:#fff;border:0}.btn-mci:hover{color:#fff;opacity:.95}footer{background:#0d2540;color:#d9e4ef}footer a{color:#d9e4ef;text-decoration:none}@media (max-width:991.98px){.admin-login-btn{display:inline-block;margin:.5rem 0 0 0}}@media (max-width:575.98px){.brand-logo{width:46px;height:46px;flex-basis:46px}.brand-copy{font-size:.9rem}.brand-copy small{font-size:.62rem}}

/* MCI V2 INNER PAGE PREMIUM LAYER */
.v2-page-hero{
 position:relative;
 overflow:hidden;
 padding:90px 0 86px;
 color:#fff;
 background:
 linear-gradient(100deg,rgba(4,24,44,.97),rgba(7,55,91,.90)),
 url('/images/mci-v2-campus-hero.webp') center/cover no-repeat;
}
.v2-page-hero .container{position:relative;z-index:2}
.v2-page-hero:after{
 content:"";
 position:absolute;
 width:380px;height:380px;
 border:1px solid rgba(255,255,255,.12);
 border-radius:50%;
 right:-120px;top:-150px;
}
.v2-kicker{
 color:#f2d58e;
 font-size:.75rem;
 font-weight:900;
 text-transform:uppercase;
 letter-spacing:.14em;
}
.v2-page-hero h1{
 max-width:850px;
 margin-top:10px;
 font-size:clamp(2.6rem,5vw,4.5rem);
 font-weight:900;
 line-height:1.03;
}
.v2-page-hero p{
 max-width:760px;
 margin-top:20px;
 color:#d8e5ef;
 font-size:1.08rem;
}
.v2-breadcrumb{
 border-bottom:1px solid #e2e9ef;
 background:#fff;
}
.v2-breadcrumb .container{
 min-height:52px;
 display:flex;
 align-items:center;
 gap:8px;
 color:#657786;
 font-size:.86rem;
}
.v2-breadcrumb a{font-weight:700}
.v2-section{padding:82px 0}
.v2-soft{background:#f4f7fa}
.v2-section-kicker{
 color:#12864c;
 font-size:.75rem;
 font-weight:900;
 text-transform:uppercase;
 letter-spacing:.13em;
}
.v2-title{
 color:#061b31;
 font-weight:900;
 line-height:1.12;
 letter-spacing:-.02em;
}
.v2-copy{
 color:#657786;
 font-size:1.02rem;
}
.v2-card{
 height:100%;
 padding:28px;
 background:#fff;
 border:1px solid #e2e9ef;
 border-radius:12px;
 transition:.22s ease;
}
.v2-card:hover{
 transform:translateY(-4px);
 box-shadow:0 16px 38px rgba(7,27,49,.09);
}
.v2-mark{
 width:48px;height:48px;
 display:grid;
 place-items:center;
 border-radius:8px;
 background:#edf6f1;
 color:#12864c;
 font-weight:900;
}
.v2-trust{
 padding:38px;
 color:#fff;
 border-radius:14px;
 background:linear-gradient(115deg,#0866b0,#12864c);
}
@media(max-width:575.98px){
 .v2-page-hero{padding:58px 0}
 .v2-section{padding:62px 0}
 .v2-trust{padding:27px 23px}
}

</style>@stack('styles')
</head>
<body>
<div class="topbar py-2"><div class="container d-flex flex-wrap justify-content-between gap-2"><div>Work under Chandrashekhar &amp; Narayan Educational Trust</div><div><a href="tel:+917004773247">7004773247</a> · <a href="tel:+919334779133">9334779133</a> · <a href="mailto:mcieducationalgroup@gmail.com">mcieducationalgroup@gmail.com</a></div></div></div>
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm"><div class="container"><a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}"><img src="{{ $logoUrl }}" alt="MCI Educational Group logo" class="brand-logo"><span class="brand-copy">MCI EDUCATIONAL GROUP<small>An Institution With Global Reach</small></span></a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mciNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="mciNav"><ul class="navbar-nav ms-auto align-items-lg-center"><li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li><li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li><li class="nav-item"><a class="nav-link" href="{{ route('institutions') }}">Institutions</a></li><li class="nav-item"><a class="nav-link" href="{{ route('programs') }}">Programs</a></li><li class="nav-item"><a class="nav-link" href="{{ route('news-events') }}">News & Events</a></li><li class="nav-item"><a class="nav-link" href="{{ route('gallery') }}">Gallery</a></li><li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li><li class="nav-item"><a class="nav-link admin-login-btn" href="{{ route('admin.login') }}">Admin Login</a></li></ul></div></div></nav>
<main>@yield('content')</main>
<footer class="pt-5 pb-3 mt-5"><div class="container"><div class="row g-4"><div class="col-md-5"><img src="{{ $logoUrl }}" alt="MCI Educational Group logo" class="footer-logo"><h5 class="text-white">MCI Educational Group</h5><p>MCI CAMPUS, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar (India)</p></div><div class="col-md-3"><h6 class="text-white">Quick Links</h6><div class="d-grid gap-2"><a href="{{ route('institutions') }}">Our Institutions</a><a href="{{ route('programs') }}">Programs</a><a href="{{ route('downloads') }}">Downloads</a><a href="{{ route('career') }}">Career</a><a href="{{ route('admin.login') }}">Admin Login</a></div></div><div class="col-md-4"><h6 class="text-white">Contact</h6><p class="mb-1">7004773247, 9334779133</p><p class="mb-0">mcieducationalgroup@gmail.com</p></div></div><hr class="border-secondary my-4"><div class="small">© {{ date('Y') }} MCI Educational Group. All rights reserved.</div></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>@stack('scripts')
</body></html>
