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
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#082c50">
    <meta property="og:type" content="website"><meta property="og:site_name" content="MCI Educational Group"><meta property="og:title" content="{{ $seoTitle }}"><meta property="og:description" content="{{ $seoDescription }}"><meta property="og:url" content="{{ $canonicalUrl }}"><meta property="og:image" content="{{ $logoUrl }}">
    <meta name="twitter:card" content="summary"><meta name="twitter:title" content="{{ $seoTitle }}"><meta name="twitter:description" content="{{ $seoDescription }}"><meta name="twitter:image" content="{{ $logoUrl }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--mci-blue:#0d4fa3;--mci-green:#1aa260;--mci-dark:#082c50;--mci-light:#f4f8fc;--mci-gold:#f0bd4f}body{font-family:Arial,Helvetica,sans-serif;color:#203047;background:#fff}.topbar{background:var(--mci-dark);color:#fff;font-size:.8rem}.topbar a{color:#fff;text-decoration:none}.topbar-inner{min-height:36px;display:flex;align-items:center;justify-content:space-between;gap:14px}.topbar-actions{display:flex;align-items:center;gap:14px;white-space:nowrap}.mainnav{background:#fff;box-shadow:0 2px 18px rgba(15,52,85,.09);z-index:1030}.navbar-brand{font-weight:800;color:var(--mci-blue)!important;min-width:245px}.brand-logo{width:58px;height:58px;object-fit:contain;flex:0 0 58px}.brand-copy{line-height:1.08;font-size:.98rem}.brand-copy small{display:block;font-size:.68rem;font-weight:600;color:#607080;margin-top:4px}.navbar-toggler{border:1px solid #d8e4ef;padding:.45rem .62rem}.navbar .nav-link{font-weight:700;font-size:.82rem;color:#21364f;padding:.75rem .55rem!important;white-space:nowrap}.navbar .nav-link:hover,.navbar .nav-link.active{color:var(--mci-blue)!important}.nav-actions{display:flex;align-items:center;gap:8px;margin-left:10px}.admin-login-btn,.app-install-btn{display:inline-flex!important;align-items:center;justify-content:center;border-radius:999px;padding:.58rem .85rem!important;font-size:.77rem!important;font-weight:800!important;white-space:nowrap}.admin-login-btn{background:linear-gradient(90deg,var(--mci-blue),var(--mci-green));color:#fff!important}.app-install-btn{border:1px solid var(--mci-blue);color:var(--mci-blue)!important;background:#fff}.app-install-btn:hover{background:#eef7ff}.footer-logo{width:82px;height:82px;object-fit:contain;background:#fff;border-radius:16px;padding:5px;margin-bottom:14px}.section-title{font-weight:800;color:var(--mci-dark)}.institution-card{height:100%;border:0;border-radius:18px;box-shadow:0 12px 30px rgba(18,54,92,.10);transition:.2s}.institution-card:hover{transform:translateY(-4px)}.btn-mci{background:linear-gradient(90deg,var(--mci-blue),var(--mci-green));color:#fff;border:0}.btn-mci:hover{color:#fff;opacity:.95}footer{background:#082c50;color:#d9e4ef}footer a{color:#d9e4ef;text-decoration:none}footer a:hover{color:#fff}.install-toast{position:fixed;right:18px;bottom:18px;z-index:1080;max-width:340px;padding:15px 18px;border-radius:12px;background:#082c50;color:#fff;box-shadow:0 16px 40px rgba(0,0,0,.24);display:none}.install-toast.show{display:block}
        @media(max-width:1199.98px){.navbar-collapse{padding:16px 0 20px;border-top:1px solid #e7eef5;margin-top:8px}.navbar-nav{align-items:stretch!important}.navbar .nav-link{font-size:.94rem;padding:.7rem .25rem!important;border-bottom:1px solid #edf2f7}.dropdown-menu{border:0;background:#f5f9fc;padding:.35rem .75rem}.nav-actions{margin:14px 0 0;display:grid;grid-template-columns:1fr 1fr}.admin-login-btn,.app-install-btn{min-height:44px}}@media(max-width:575.98px){.topbar-inner{align-items:flex-start;flex-direction:column;padding:7px 0}.topbar-actions{gap:9px;flex-wrap:wrap}.brand-logo{width:46px;height:46px;flex-basis:46px}.navbar-brand{min-width:0;max-width:78%}.brand-copy{font-size:.82rem}.brand-copy small{font-size:.58rem}.nav-actions{grid-template-columns:1fr}}

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
.institution-card{position:relative;overflow:hidden;background:#fff}
.institution-accent{height:5px;background:linear-gradient(90deg,var(--mci-blue),var(--mci-green))}
.institution-body{padding:28px}
.institution-logo-wrap{width:84px;height:84px;display:grid;place-items:center;padding:7px;border:1px solid #dfeaf3;border-radius:14px;background:#fff;overflow:hidden}
.institution-logo-wrap .institution-logo{display:block;width:100%;height:100%;max-width:100%;object-fit:contain;margin:0}
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
<div class="topbar"><div class="container-xl px-3 topbar-inner"><div>Run Under <strong>Chandrashekhar &amp; Narayan Educational Trust</strong></div><div class="topbar-actions"><a href="tel:+917004773247">7004773247</a><a href="mailto:mcieducationalgroup@gmail.com">mcieducationalgroup@gmail.com</a><a href="{{ route('admin.login') }}">Admin Login</a></div></div></div>
<nav class="navbar navbar-expand-xl mainnav sticky-top"><div class="container-xl px-3"><a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}"><img src="{{ $logoUrl }}" alt="MCI Educational Group logo" class="brand-logo"><span class="brand-copy">MCI EDUCATIONAL GROUP<small>Education • Skills • Technology</small></span></a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mciNav" aria-controls="mciNav" aria-expanded="false" aria-label="Open navigation menu"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="mciNav"><ul class="navbar-nav ms-auto align-items-xl-center"><li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li><li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li><li class="nav-item"><a class="nav-link" href="{{ route('institutions') }}">Institutions</a></li><li class="nav-item"><a class="nav-link" href="{{ route('programs') }}">Programs</a></li><li class="nav-item"><a class="nav-link" href="{{ route('news-events') }}">News</a></li><li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">More</a><ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('gallery') }}">Gallery</a></li><li><a class="dropdown-item" href="{{ route('downloads') }}">Downloads</a></li><li><a class="dropdown-item" href="{{ route('career') }}">Career</a></li><li><a class="dropdown-item" href="{{ route('contact') }}">Contact Us</a></li></ul></li></ul><div class="nav-actions"><button type="button" class="app-install-btn mci-install-trigger">📲 Install App</button><a class="admin-login-btn" href="{{ route('admin.login') }}">Admin Login</a></div></div></div></nav>
<main>@yield('content')</main>
<footer class="pt-5 pb-3"><div class="container-xl px-3"><div class="row g-4"><div class="col-md-5"><img src="{{ $logoUrl }}" alt="MCI Educational Group logo" class="footer-logo"><h5 class="text-white">MCI Educational Group</h5><p>MCI CAMPUS, Vandana Cinema Road, Opp. Hotel Gulmarg, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar (India)</p></div><div class="col-md-3"><h6 class="text-white">Quick Links</h6><div class="d-grid gap-2"><a href="{{ route('institutions') }}">Our Institutions</a><a href="{{ route('programs') }}">Programs</a><a href="{{ route('downloads') }}">Downloads</a><a href="{{ route('career') }}">Career</a><a href="{{ route('admin.login') }}">Admin Login</a></div></div><div class="col-md-4"><h6 class="text-white">Contact</h6><p class="mb-1">7004773247, 9334779133</p><p class="mb-2">mcieducationalgroup@gmail.com</p><button type="button" class="app-install-btn mci-install-trigger">📲 Install Mobile App</button></div></div><hr class="border-secondary my-4"><div class="small d-flex justify-content-between flex-wrap gap-2"><span>© {{ date('Y') }} MCI Educational Group. All rights reserved.</span><span>Official Institutional Portal</span></div></div></footer>
<div class="install-toast" id="installToast" role="status"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script>let mciInstallPrompt=null;const installButtons=document.querySelectorAll('.mci-install-trigger');const installToast=document.getElementById('installToast');function showInstallMessage(message){if(!installToast)return;installToast.textContent=message;installToast.classList.add('show');setTimeout(()=>installToast.classList.remove('show'),4500)}window.addEventListener('beforeinstallprompt',event=>{event.preventDefault();mciInstallPrompt=event});installButtons.forEach(button=>button.addEventListener('click',async()=>{if(mciInstallPrompt){mciInstallPrompt.prompt();await mciInstallPrompt.userChoice;mciInstallPrompt=null;return}if(window.matchMedia('(display-mode: standalone)').matches){showInstallMessage('MCI app is already installed on this device.');return}const isiOS=/iphone|ipad|ipod/i.test(navigator.userAgent);showInstallMessage(isiOS?'Safari में Share खोलकर “Add to Home Screen” चुनिए।':'Browser menu खोलकर “Install app” या “Add to Home screen” चुनिए।')}));if('serviceWorker' in navigator){window.addEventListener('load',()=>navigator.serviceWorker.register('/service-worker.js').catch(()=>{}))}</script>@stack('scripts')
</body></html>
