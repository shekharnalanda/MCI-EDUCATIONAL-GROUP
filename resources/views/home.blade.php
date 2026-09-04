<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MCI Educational Group | Education, Skills & Digital Services</title>

<meta name="description" content="MCI Educational Group brings together education, skill development, knowledge and digital services under Chandrashekhar & Narayan Educational Trust.">

<link rel="canonical" href="https://mciedu.in/">
<link rel="icon" type="image/png" href="{{ asset('images/mci-logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/mci-logo.png') }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="MCI Educational Group">
<meta property="og:title" content="MCI Educational Group">
<meta property="og:description" content="A connected institutional ecosystem for education, skills, knowledge and digital services.">
<meta property="og:url" content="https://mciedu.in/">
<meta property="og:image" content="{{ asset('images/mci-logo.png') }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="MCI Educational Group">
<meta name="twitter:description" content="Education, skills, knowledge and digital services through one connected institutional ecosystem.">
<meta name="twitter:image" content="{{ asset('images/mci-logo.png') }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root{
    --mci-navy:#061b31;
    --mci-navy-2:#0b2948;
    --mci-blue:#0866b0;
    --mci-green:#12864c;
    --mci-green-dark:#0d6d3d;
    --mci-gold:#d4aa4e;
    --mci-bg:#f4f7fa;
    --mci-ink:#1a2c3b;
    --mci-muted:#657786;
    --mci-border:#e2e9ef;
    --mci-white:#fff;
}

*{box-sizing:border-box}
html{scroll-behavior:smooth}

:focus-visible{
    outline:3px solid #f1c75b;
    outline-offset:3px;
}

.skip-link{
    position:absolute;
    left:-9999px;
    top:10px;
    z-index:99999;
    padding:10px 16px;
    background:#fff;
    color:#061b31;
    font-weight:800;
    border-radius:5px;
}

.skip-link:focus{
    left:10px;
}

img{
    max-width:100%;
}

@media(prefers-reduced-motion:reduce){
    html{scroll-behavior:auto}

    *,
    *:before,
    *:after{
        scroll-behavior:auto!important;
        transition:none!important;
        animation:none!important;
    }
}

body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    color:var(--mci-ink);
    background:#fff;
    line-height:1.65;
}

a{text-decoration:none}

.topbar{
    background:var(--mci-navy);
    color:#d9e5ee;
    font-size:.82rem;
}

.topbar-inner{
    min-height:38px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
}

.topbar a{color:#fff}

.topbar-contact{
    display:flex;
    flex-wrap:wrap;
    gap:18px;
}

.main-nav{
    background:rgba(255,255,255,.98);
    border-bottom:1px solid rgba(7,27,49,.06);
    box-shadow:0 7px 25px rgba(7,27,49,.06);
}

.brand-logo{
    width:66px;
    height:66px;
    object-fit:contain;
}

.brand-title{
    display:block;
    color:var(--mci-navy);
    font-size:1rem;
    font-weight:900;
    letter-spacing:.04em;
}

.brand-sub{
    display:block;
    margin-top:2px;
    color:var(--mci-green);
    font-size:.66rem;
    font-weight:800;
    letter-spacing:.1em;
    text-transform:uppercase;
}

.nav-link{
    color:#2f4355!important;
    font-weight:700;
    font-size:.92rem;
    padding:.72rem .67rem!important;
}

.nav-link:hover{color:var(--mci-blue)!important}

.admin-btn{
    background:var(--mci-navy);
    color:#fff!important;
    border-radius:6px;
    padding:.64rem 1rem!important;
    margin-left:.35rem;
}

.hero{
    position:relative;
    min-height:680px;
    display:flex;
    align-items:center;
    overflow:hidden;
    color:#fff;
    background:
      linear-gradient(90deg,rgba(4,24,44,.97) 0%,rgba(7,48,83,.91) 52%,rgba(8,84,102,.67) 100%),
      url('/images/mci-v2-campus-hero.webp')
      center/cover no-repeat;
}

.hero:after{
    content:"";
    position:absolute;
    width:520px;
    height:520px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:50%;
    right:-180px;
    top:-180px;
}

.hero-content{
    position:relative;
    z-index:2;
    max-width:900px;
}

.hero-kicker{
    display:inline-flex;
    align-items:center;
    gap:10px;
    color:#f1d58f;
    font-size:.78rem;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.15em;
}

.hero-kicker:before{
    content:"";
    width:34px;
    height:2px;
    background:var(--mci-gold);
}

.hero h1{
    max-width:880px;
    margin:22px 0 0;
    font-size:clamp(3rem,6vw,5.7rem);
    font-weight:900;
    line-height:.99;
    letter-spacing:-.045em;
}

.hero h1 span{color:#8ed1aa}

.hero-lead{
    max-width:760px;
    margin-top:27px;
    color:#d8e6ef;
    font-size:1.17rem;
}

.hero-actions{
    display:flex;
    flex-wrap:wrap;
    gap:13px;
    margin-top:34px;
}

.btn-mci{
    display:inline-block;
    padding:14px 22px;
    border-radius:6px;
    background:var(--mci-green);
    border:1px solid var(--mci-green);
    color:#fff;
    font-weight:800;
}

.btn-mci:hover{
    background:var(--mci-green-dark);
    color:#fff;
}

.btn-hero-outline{
    display:inline-block;
    padding:14px 22px;
    border:1px solid rgba(255,255,255,.55);
    border-radius:6px;
    color:#fff;
    font-weight:800;
}

.btn-hero-outline:hover{
    background:#fff;
    color:var(--mci-navy);
}

.hero-meta{
    display:flex;
    flex-wrap:wrap;
    gap:34px;
    margin-top:48px;
    padding-top:25px;
    border-top:1px solid rgba(255,255,255,.18);
}

.hero-meta strong{
    display:block;
    color:#fff;
    font-size:.98rem;
}

.hero-meta span{
    display:block;
    color:#b9cbd9;
    font-size:.8rem;
}

.identity-wrap{
    position:relative;
    z-index:4;
    margin-top:-44px;
}

.identity-panel{
    overflow:hidden;
    background:#fff;
    border-radius:10px;
    box-shadow:0 20px 50px rgba(7,27,49,.15);
}

.identity-item{
    height:100%;
    padding:24px 26px;
    border-right:1px solid var(--mci-border);
}

.identity-item:last-child{border-right:0}

.identity-label{
    color:var(--mci-green);
    font-size:.7rem;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.12em;
}

.identity-value{
    margin-top:5px;
    color:var(--mci-navy);
    font-weight:800;
}

.section{padding:92px 0}
.section-soft{background:var(--mci-bg)}

.section-kicker{
    color:var(--mci-green);
    font-size:.76rem;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.14em;
}

.section-title{
    color:var(--mci-navy);
    font-weight:900;
    line-height:1.12;
    letter-spacing:-.025em;
}

.section-copy{
    color:var(--mci-muted);
    font-size:1.04rem;
}

.about-lead{
    padding-left:22px;
    border-left:4px solid var(--mci-gold);
}

.value-card{
    height:100%;
    padding:29px;
    background:#fff;
    border:1px solid var(--mci-border);
    border-radius:12px;
}

.value-number{
    color:#d7e3ec;
    font-size:2.4rem;
    line-height:1;
    font-weight:900;
}

.value-card h5{
    margin-top:18px;
    color:var(--mci-navy);
    font-weight:850;
}

.institution-card{
    height:100%;
    overflow:hidden;
    background:#fff;
    border:1px solid var(--mci-border);
    border-radius:12px;
    transition:.24s ease;
}

.institution-card:hover{
    transform:translateY(-6px);
    border-color:#cfdee9;
    box-shadow:0 20px 44px rgba(7,27,49,.10);
}

.institution-top{
    height:6px;
    background:linear-gradient(90deg,var(--mci-blue),var(--mci-green));
}

.institution-body{padding:29px}

.institution-logo-wrap{
    width:82px;
    height:82px;
    display:grid;
    place-items:center;
    overflow:hidden;
    padding:7px;
    background:#fff;
    border:1px solid var(--mci-border);
    border-radius:12px;
    box-shadow:0 8px 20px rgba(7,27,49,.06);
}

.institution-logo{
    width:100%;
    height:100%;
    object-fit:contain;
}

.institution-fallback{
    width:100%;
    height:100%;
    display:grid;
    place-items:center;
    color:var(--mci-blue);
    font-weight:900;
}

.institution-name{
    margin-top:21px;
    color:var(--mci-navy);
    font-weight:900;
}

.institution-desc{
    min-height:72px;
    color:var(--mci-muted);
}

.institution-link{
    color:var(--mci-blue);
    font-weight:800;
}

.institution-link:hover{color:var(--mci-green)}

.stats{
    background:var(--mci-navy);
    color:#fff;
}

.stats-copy{color:#b7c9d8}

.stat-box{
    height:100%;
    padding:30px 26px;
    border-left:1px solid rgba(255,255,255,.16);
}

.stat-number{
    color:#fff;
    font-size:clamp(2.4rem,5vw,4.1rem);
    line-height:1;
    font-weight:900;
}

.stat-label{
    margin-top:10px;
    color:#b9cad8;
    font-weight:700;
}

.achievement-card{
    height:100%;
    padding:29px;
    background:#fff;
    border:1px solid var(--mci-border);
    border-radius:12px;
}

.achievement-mark{
    width:48px;
    height:48px;
    display:grid;
    place-items:center;
    background:#fff7e4;
    color:#966d16;
    border-radius:8px;
    font-weight:900;
}

.achievement-card h5{
    margin-top:20px;
    color:var(--mci-navy);
    font-weight:850;
}

.news-card,
.gallery-card{
    height:100%;
    background:#fff;
    border:1px solid var(--mci-border);
    border-radius:12px;
    overflow:hidden;
}

.news-card .card-body{padding:27px}

.gallery-card img{
    width:100%;
    height:230px;
    object-fit:cover;
}

.gallery-card .card-body{padding:20px}

.cta-panel{
    padding:44px;
    border-radius:14px;
    color:#fff;
    background:linear-gradient(115deg,var(--mci-blue),var(--mci-green));
}

.footer{
    padding:68px 0 28px;
    background:#041526;
    color:#acbdcb;
}

.footer-logo{
    width:88px;
    height:88px;
    object-fit:contain;
    padding:5px;
    background:#fff;
    border-radius:10px;
}

.footer h5{
    color:#fff;
    font-weight:850;
}

.footer a{color:#acbdcb}
.footer a:hover{color:#fff}

.premium-ribbon{
    background:#fff;
    border-bottom:1px solid var(--mci-border);
}

.premium-ribbon-inner{
    min-height:54px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-wrap:wrap;
    gap:12px 30px;
    color:#43596b;
    font-size:.85rem;
    font-weight:700;
}

.premium-ribbon-item{
    display:flex;
    align-items:center;
    gap:8px;
}

.premium-dot{
    width:7px;
    height:7px;
    border-radius:50%;
    background:var(--mci-green);
}

.hero-badge{
    display:inline-flex;
    align-items:center;
    margin-top:22px;
    padding:8px 12px;
    border:1px solid rgba(255,255,255,.18);
    border-radius:6px;
    background:rgba(255,255,255,.07);
    color:#dce8f2;
    font-size:.8rem;
    font-weight:700;
}

.quick-access{
    margin-top:-1px;
    background:#fff;
    border-bottom:1px solid var(--mci-border);
}

.quick-card{
    display:block;
    height:100%;
    padding:28px 24px;
    border-right:1px solid var(--mci-border);
    color:var(--mci-navy);
    transition:.2s ease;
}

.quick-card:hover{
    background:var(--mci-bg);
    color:var(--mci-blue);
}

.quick-card small{
    display:block;
    margin-bottom:5px;
    color:var(--mci-green);
    font-size:.7rem;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.11em;
}

.quick-card strong{
    display:block;
    font-size:1rem;
}

/* Academic section visual balance refinement */
.academic-panel .row{
    align-items:stretch;
}

.academic-panel .col-lg-7,
.academic-panel .col-lg-5{
    display:flex;
}

.academic-panel-main,
.academic-side{
    width:100%;
}

.academic-panel-main{
    padding:38px 42px;
}

.academic-panel-main .display-5{
    max-width:760px;
    font-size:clamp(2rem,3.15vw,3.35rem);
    line-height:1.08;
}

.academic-panel-main .section-copy{
    max-width:760px;
    margin-bottom:0;
}

.academic-list{
    margin-top:20px;
}

.academic-list li{
    padding-top:10px;
    padding-bottom:10px;
}

.academic-side{
    display:flex;
    flex-direction:column;
    justify-content:center;
    min-height:100%;
    padding:38px 42px;
}

.academic-side h3{
    max-width:430px;
    font-size:clamp(1.65rem,2.2vw,2.2rem);
    line-height:1.12;
}

.academic-side p{
    max-width:470px;
    margin-bottom:0;
}

.academic-side .btn{
    align-self:flex-start;
    margin-top:24px!important;
}

@media(max-width:991.98px){
    .academic-panel .col-lg-7,
    .academic-panel .col-lg-5{
        display:block;
    }

    .academic-panel-main,
    .academic-side{
        padding:32px 28px;
    }

    .academic-side{
        min-height:auto;
    }
}


.academic-panel{
    overflow:hidden;
    border:1px solid var(--mci-border);
    border-radius:14px;
    background:#fff;
    box-shadow:0 16px 42px rgba(7,27,49,.06);
}

.academic-panel-main{
    padding:42px;
}

.academic-side{
    height:100%;
    padding:42px;
    background:var(--mci-navy);
    color:#fff;
}

.academic-side p{
    color:#b8c9d7;
}

.academic-list{
    padding:0;
    margin:24px 0 0;
    list-style:none;
}

.academic-list li{
    position:relative;
    padding:12px 0 12px 28px;
    border-bottom:1px solid rgba(7,27,49,.08);
    color:#536879;
}

.academic-list li:before{
    content:"✓";
    position:absolute;
    left:0;
    color:var(--mci-green);
    font-weight:900;
}

.governance-section{
    background:#eef5f1;
}

.governance-card{
    height:100%;
    padding:34px;
    border:1px solid #dce9e1;
    border-radius:12px;
    background:#fff;
}

.governance-card h4{
    color:var(--mci-navy);
    font-weight:900;
}

.governance-label{
    color:var(--mci-green);
    font-size:.72rem;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.12em;
}

.admission-panel{
    overflow:hidden;
    border-radius:16px;
    box-shadow:0 22px 55px rgba(7,27,49,.12);
}

.admission-main{
    height:100%;
    padding:48px;
    background:linear-gradient(120deg,var(--mci-blue),#07538c);
    color:#fff;
}

.admission-main p{
    color:#dce9f3;
}

.admission-actions{
    height:100%;
    padding:42px;
    background:#fff;
}

.admission-step{
    display:flex;
    align-items:flex-start;
    gap:16px;
    padding:16px 0;
    border-bottom:1px solid var(--mci-border);
}

.admission-step:last-child{
    border-bottom:0;
}

.step-number{
    flex:0 0 38px;
    height:38px;
    display:grid;
    place-items:center;
    border-radius:50%;
    background:var(--mci-bg);
    color:var(--mci-blue);
    font-weight:900;
}

.admission-step strong{
    display:block;
    color:var(--mci-navy);
}

.admission-step span{
    display:block;
    color:var(--mci-muted);
    font-size:.9rem;
}

.performance-note{
    font-size:.78rem;
    color:var(--mci-muted);
}


@media(max-width:991.98px){
    .admin-btn{margin:.5rem 0 0}
    .hero{min-height:620px}
    .identity-wrap{margin-top:0}
    .identity-panel{border-radius:0}
    .identity-item{
        border-right:0;
        border-bottom:1px solid var(--mci-border);
    }
}

@media(max-width:575.98px){
    .topbar-inner{
        display:block;
        padding:8px 0;
    }

    .topbar-contact{
        margin-top:4px;
        gap:9px;
    }

    .brand-logo{
        width:52px;
        height:52px;
    }

    .brand-title{font-size:.81rem}
    .brand-sub{font-size:.54rem}

    .hero{
        min-height:590px;
    }

    .hero h1{
        font-size:2.7rem;
    }

    .hero-meta{
        gap:18px;
    }

    .section{
        padding:68px 0;
    }

    .cta-panel{
        padding:32px 25px;
    }

    .premium-ribbon-inner{
        justify-content:flex-start;
        gap:7px 16px;
        padding:10px 0;
    }

    .quick-card{
        padding:20px 14px;
        border-bottom:1px solid var(--mci-border);
    }

    .academic-panel-main,
    .academic-side,
    .admission-main,
    .admission-actions{
        padding:30px 24px;
    }

    .institution-body{
        padding:24px;
    }

    .hero-actions a{
        width:100%;
        text-align:center;
    }
}
</style>

@verbatim
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"EducationalOrganization",
  "name":"MCI Educational Group",
  "url":"https://mciedu.in/",
  "logo":"https://mciedu.in/images/mci-logo.png",
  "description":"MCI Educational Group - education, skills, knowledge and digital services.",
  "address":{
    "@type":"PostalAddress",
    "streetAddress":"MCI Campus, Quamruddin Ganj",
    "addressLocality":"Bihar Sharif",
    "addressRegion":"Bihar",
    "postalCode":"803101",
    "addressCountry":"IN"
  }
}
</script>
@endverbatim

</head>

<body>

<a class="skip-link" href="#main-content">
Skip to main content
</a>

<header>

<div class="topbar">
<div class="container">
<div class="topbar-inner">

<div>
Working under
<strong>Chandrashekhar &amp; Narayan Educational Trust</strong>
</div>

<div class="topbar-contact">
<span>7004773247</span>
<span>9334779133</span>
<a href="mailto:mcieducationalgroup@gmail.com">
mcieducationalgroup@gmail.com
</a>
</div>

</div>
</div>
</div>


<div class="premium-ribbon">
<div class="container">
<div class="premium-ribbon-inner">

<div class="premium-ribbon-item">
<span class="premium-dot"></span>
Education &amp; Learning
</div>

<div class="premium-ribbon-item">
<span class="premium-dot"></span>
Skill Development
</div>

<div class="premium-ribbon-item">
<span class="premium-dot"></span>
Digital Platforms
</div>

<div class="premium-ribbon-item">
<span class="premium-dot"></span>
Community Services
</div>

</div>
</div>
</div>


<nav class="navbar navbar-expand-lg main-nav sticky-top">
<div class="container py-2">

<a class="navbar-brand d-flex align-items-center gap-3" href="/">
<img
    src="{{ asset('images/mci-logo.png') }}"
    alt="MCI Educational Group logo"
    class="brand-logo"
    width="66"
    height="66"
>
<span>
<span class="brand-title">MCI EDUCATIONAL GROUP</span>
<span class="brand-sub">An Institution With Global Reach</span>
</span>
</a>

<button
    class="navbar-toggler"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#mciMainNav"
aria-controls="mciMainNav"
aria-expanded="false"
aria-label="Toggle navigation"
>
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="mciMainNav">
<ul class="navbar-nav ms-auto align-items-lg-center">

<li class="nav-item"><a class="nav-link" href="/">Home</a></li>
<li class="nav-item"><a class="nav-link" href="/about">About</a></li>
<li class="nav-item"><a class="nav-link" href="/institutions">Institutions</a></li>
<li class="nav-item"><a class="nav-link" href="/programs">Programs</a></li>
<li class="nav-item"><a class="nav-link" href="/news-events">News &amp; Events</a></li>
<li class="nav-item"><a class="nav-link" href="/gallery">Gallery</a></li>
<li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>

<li class="nav-item">
<a class="nav-link admin-btn" href="{{ route('admin.login') }}">
Admin Login
</a>
</li>

</ul>
</div>

</div>
</nav>

</header>


<main id="main-content">

<section class="hero">
<div class="container">

<div class="hero-content">

<div class="hero-kicker">
Education • Skills • Knowledge • Digital Growth
</div>

<h1>
Building institutions.
<span>Empowering futures.</span>
</h1>

<p class="hero-lead">
MCI Educational Group is a connected institutional ecosystem
committed to accessible education, practical skills, digital
empowerment and dependable professional services for learners,
families and communities.
</p>

<div class="hero-badge">
A professionally managed institutional ecosystem under
Chandrashekhar &amp; Narayan Educational Trust
</div>


<div class="hero-actions">
<a href="/institutions" class="btn-mci">
Explore Our Institutions
</a>

<a href="/contact" class="btn-hero-outline">
Admission &amp; Enquiry
</a>
</div>

<div class="hero-meta">

<div>
<strong>Education &amp; Training</strong>
<span>Academic and career-focused learning</span>
</div>

<div>
<strong>Digital Ecosystem</strong>
<span>Connected institutional platforms</span>
</div>

<div>
<strong>Bihar Sharif, Nalanda</strong>
<span>Serving learners and communities</span>
</div>

</div>

</div>
</div>
</section>


<section class="identity-wrap">
<div class="container">
<div class="identity-panel">

<div class="row g-0">

<div class="col-lg-4">
<div class="identity-item">
<div class="identity-label">Institutional Identity</div>
<div class="identity-value">
MCI Educational Group
</div>
</div>
</div>

<div class="col-lg-4">
<div class="identity-item">
<div class="identity-label">Operating Framework</div>
<div class="identity-value">
Chandrashekhar &amp; Narayan Educational Trust
</div>
</div>
</div>

<div class="col-lg-4">
<div class="identity-item">
<div class="identity-label">Campus</div>
<div class="identity-value">
Quamruddin Ganj, Bihar Sharif, Nalanda
</div>
</div>
</div>

</div>

</div>
</div>
</section>


<section class="quick-access">
<div class="container">

<div class="row g-0">

<div class="col-6 col-lg-3">
<a href="/institutions" class="quick-card">
<small>Explore</small>
<strong>Our Institutions →</strong>
</a>
</div>

<div class="col-6 col-lg-3">
<a href="/programs" class="quick-card">
<small>Learn</small>
<strong>Programs &amp; Courses →</strong>
</a>
</div>

<div class="col-6 col-lg-3">
<a href="/news-events" class="quick-card">
<small>Discover</small>
<strong>News &amp; Events →</strong>
</a>
</div>

<div class="col-6 col-lg-3">
<a href="/contact" class="quick-card">
<small>Connect</small>
<strong>Admission &amp; Enquiry →</strong>
</a>
</div>

</div>

</div>
</section>



<section class="section">
<div class="container">

<div class="row g-5 align-items-center">

<div class="col-lg-6">

<div class="section-kicker">
About MCI Educational Group
</div>

<h2 class="section-title display-5 mt-2">
A unified platform for learning, skills and modern services.
</h2>

</div>

<div class="col-lg-6">

<div class="about-lead">

<p class="section-copy mb-0">
MCI Educational Group brings together multiple educational,
training and service initiatives under one institutional identity.
Our focus is to create accessible learning opportunities, practical
skill development, digital resources and dependable services through
a professionally managed and continuously growing ecosystem.
</p>

</div>

</div>

</div>


<div class="row g-4 mt-4">

<div class="col-md-6 col-xl-3">
<div class="value-card">
<div class="value-number">01</div>
<h5>Quality Education</h5>
<p class="section-copy mb-0">
Clear, disciplined and learner-focused academic experiences.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="value-card">
<div class="value-number">02</div>
<h5>Career Skills</h5>
<p class="section-copy mb-0">
Practical and technology-enabled learning for future opportunities.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="value-card">
<div class="value-number">03</div>
<h5>Digital Access</h5>
<p class="section-copy mb-0">
Connected online platforms for education, services and information.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="value-card">
<div class="value-number">04</div>
<h5>Responsible Growth</h5>
<p class="section-copy mb-0">
A scalable institutional framework ready for future initiatives.
</p>
</div>
</div>

</div>

</div>
</section>


<section class="section">
<div class="container">

<div class="academic-panel">

<div class="row g-0">

<div class="col-lg-7">

<div class="academic-panel-main">

<div class="section-kicker">
Academic &amp; Institutional Approach
</div>

<h2 class="section-title display-5 mt-2">
Learning designed around knowledge, skills and real-world relevance.
</h2>

<p class="section-copy mt-3">
Our institutional model combines academic learning, practical skill
development, digital access and community-focused services within a
single coordinated ecosystem.
</p>

<ul class="academic-list">

<li>
Learner-focused education and training environment
</li>

<li>
Practical technology and career-oriented skill development
</li>

<li>
Connected digital systems supporting institutional access
</li>

<li>
Scalable structure for future programs and institutions
</li>

</ul>

</div>

</div>

<div class="col-lg-5">

<div class="academic-side">

<div class="section-kicker text-warning">
Our Direction
</div>

<h3 class="fw-bold mt-3">
Education that continues beyond the classroom.
</h3>

<p class="mt-3">
MCI Educational Group is being developed as a modern education and
services network where learners can move from foundational learning
to skills, digital resources and professional opportunities.
</p>

<a href="/about" class="btn btn-light fw-bold mt-3">
Know More About MCI
</a>

</div>

</div>

</div>

</div>

</div>
</section>



<section class="section section-soft">
<div class="container">

<div class="row align-items-end g-4 mb-5">

<div class="col-lg-7">
<div class="section-kicker">
Our Institutions &amp; Services
</div>

<h2 class="section-title display-5 mt-2 mb-0">
One group. Multiple opportunities.
</h2>
</div>

<div class="col-lg-5">
<p class="section-copy mb-0">
Explore the institutions and professional services that form the
MCI Educational Group ecosystem.
</p>
</div>

</div>


<div class="row g-4">

@forelse($institutions as $item)

@php
$words = preg_split('/\s+/', trim($item->name));
$institutionCode = strtoupper(
    collect($words)
        ->filter()
        ->map(fn($word) => substr($word,0,1))
        ->implode('')
);
@endphp

<div class="col-md-6 col-xl-4">

<article class="institution-card">

<div class="institution-top"></div>

<div class="institution-body">

<div class="institution-logo-wrap">

@if($item->logo || $item->image)

<img
    src="{{ $item->logo ?: $item->image }}"
    alt="{{ $item->name }} logo"
    class="institution-logo"
    width="68"
    height="68"
    loading="lazy"
    decoding="async"
    onerror="this.style.display='none';this.nextElementSibling.style.display='grid';"
>

<span
    class="institution-fallback"
    style="display:none"
>
{{ $institutionCode }}
</span>

@else

<span class="institution-fallback">
{{ $institutionCode }}
</span>

@endif

</div>

<h3 class="institution-name h4">
{{ $item->name }}
</h3>

<p class="institution-desc">
{{ $item->short_description ?: $item->description }}
</p>

@if($item->website_url)

<a
    class="institution-link"
    href="{{ $item->website_url }}"
    target="_blank"
    rel="noopener"
>
Visit Official Website →
</a>

@endif

</div>
</article>
</div>

@empty

<div class="col-12">
<div class="alert alert-light border text-center">
Institution information will be published soon.
</div>
</div>

@endforelse

</div>

</div>
</section>


<section class="section governance-section">
<div class="container">

<div class="row g-5 align-items-center">

<div class="col-lg-5">

<div class="section-kicker">
Trust &amp; Governance
</div>

<h2 class="section-title display-5 mt-2">
A common institutional framework with responsible management.
</h2>

<p class="section-copy mt-3 mb-0">
The institutions and services of MCI Educational Group operate under
the broader framework of Chandrashekhar &amp; Narayan Educational Trust,
supporting coordinated administration, institutional continuity and
future development.
</p>

</div>

<div class="col-lg-7">

<div class="row g-4">

<div class="col-md-6">
<div class="governance-card">

<div class="governance-label">
Group Identity
</div>

<h4 class="mt-3">
MCI Educational Group
</h4>

<p class="section-copy mb-0">
A unified public identity connecting education, training,
digital platforms and professional services.
</p>

</div>
</div>

<div class="col-md-6">
<div class="governance-card">

<div class="governance-label">
Trust Framework
</div>

<h4 class="mt-3">
Chandrashekhar &amp; Narayan Educational Trust
</h4>

<p class="section-copy mb-0">
Supporting institutional governance, coordination and
long-term educational development.
</p>

</div>
</div>

</div>

</div>

</div>

</div>
</section>



<section class="section stats">
<div class="container">

<div class="row align-items-center g-5">

<div class="col-lg-5">

<div class="section-kicker text-warning">
Our Growing Network
</div>

<h2 class="display-5 fw-bold mt-2">
A connected institutional ecosystem.
</h2>

<p class="stats-copy mt-3 mb-0">
The group platform is designed to bring institutions, learners,
services and digital systems together under one professionally
managed structure.
</p>

</div>

<div class="col-lg-7">

<div class="row g-0">

<div class="col-6 col-md-3">
<div class="stat-box">
<div class="stat-number">
{{ $institutions->count() }}
</div>
<div class="stat-label">
Active Institutions &amp; Services
</div>
</div>
</div>

<div class="col-6 col-md-3">
<div class="stat-box">
<div class="stat-number">01</div>
<div class="stat-label">
Unified Group Platform
</div>
</div>
</div>

<div class="col-6 col-md-3">
<div class="stat-box">
<div class="stat-number">24×7</div>
<div class="stat-label">
Digital Information Access
</div>
</div>
</div>

<div class="col-6 col-md-3">
<div class="stat-box">
<div class="stat-number">∞</div>
<div class="stat-label">
Future Growth Potential
</div>
</div>
</div>

</div>

</div>

</div>

</div>
</section>


<section class="section">
<div class="container">

<div class="text-center mx-auto mb-5" style="max-width:760px">

<div class="section-kicker">
Institutional Strength
</div>

<h2 class="section-title display-5 mt-2">
Built for education today and growth tomorrow.
</h2>

</div>

<div class="row g-4">

<div class="col-md-6 col-xl-3">
<div class="achievement-card">
<div class="achievement-mark">A</div>
<h5>Central Administration</h5>
<p class="section-copy mb-0">
A unified management framework supporting multiple institutions.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="achievement-card">
<div class="achievement-mark">D</div>
<h5>Digital Infrastructure</h5>
<p class="section-copy mb-0">
Web-based systems connecting information, services and learners.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="achievement-card">
<div class="achievement-mark">L</div>
<h5>Learner Focus</h5>
<p class="section-copy mb-0">
Education and skill initiatives designed around practical value.
</p>
</div>
</div>

<div class="col-md-6 col-xl-3">
<div class="achievement-card">
<div class="achievement-mark">G</div>
<h5>Growth Ready</h5>
<p class="section-copy mb-0">
A scalable platform for new institutions, programs and services.
</p>
</div>
</div>

</div>

</div>
</section>


@if($newsItems->count())

<section class="section section-soft">
<div class="container">

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-5">

<div>
<div class="section-kicker">Latest Updates</div>
<h2 class="section-title display-6 mb-0">
News &amp; Events
</h2>
</div>

<a href="/news-events" class="btn btn-outline-primary">
View All
</a>

</div>

<div class="row g-4">

@foreach($newsItems as $item)

<div class="col-md-6 col-lg-4">

<article class="news-card">

<div class="card-body">

<div class="small text-success fw-bold">
{{ optional($item->published_at)->format('d M Y') ?: 'MCI Update' }}
</div>

<h3 class="h5 fw-bold mt-2">
{{ $item->title }}
</h3>

<p class="section-copy mb-0">
{{ $item->excerpt }}
</p>

</div>

</article>

</div>

@endforeach

</div>

</div>
</section>

@endif


@if($galleryItems->count())

<section class="section">
<div class="container">

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-5">

<div>
<div class="section-kicker">Campus &amp; Activities</div>
<h2 class="section-title display-6 mb-0">
Gallery Highlights
</h2>
</div>

<a href="/gallery" class="btn btn-outline-primary">
View Gallery
</a>

</div>

<div class="row g-4">

@foreach($galleryItems as $item)

<div class="col-md-6 col-lg-4">

<div class="gallery-card">

<img
    src="{{ $item->image }}"
    alt="{{ $item->title }}"
    loading="lazy"
    decoding="async"
>

<div class="card-body">

<h3 class="h6 fw-bold mb-1">
{{ $item->title }}
</h3>

@if($item->caption)

<p class="section-copy mb-0">
{{ $item->caption }}
</p>

@endif

</div>

</div>

</div>

@endforeach

</div>

</div>
</section>

@endif


<section class="section section-soft">
<div class="container">

<div class="admission-panel">

<div class="row g-0">

<div class="col-lg-7">

<div class="admission-main">

<div class="section-kicker text-warning">
Admissions &amp; Enquiries
</div>

<h2 class="display-5 fw-bold mt-3">
Find the right institution or service within the MCI network.
</h2>

<p class="mt-3">
Whether you are looking for school education, computer training,
learning resources, digital services or other institutional support,
our central enquiry pathway can guide you to the relevant unit.
</p>

<a href="/contact" class="btn btn-light btn-lg fw-bold mt-3">
Start Your Enquiry
</a>

</div>

</div>

<div class="col-lg-5">

<div class="admission-actions">

<div class="admission-step">

<div class="step-number">1</div>

<div>
<strong>Choose your requirement</strong>
<span>
Select the institution, program or service you are interested in.
</span>
</div>

</div>

<div class="admission-step">

<div class="step-number">2</div>

<div>
<strong>Submit your enquiry</strong>
<span>
Share your contact and basic requirement through the central system.
</span>
</div>

</div>

<div class="admission-step">

<div class="step-number">3</div>

<div>
<strong>Receive guidance</strong>
<span>
Your enquiry can be directed to the appropriate institution or team.
</span>
</div>

</div>

</div>

</div>

</div>

</div>

</div>
</section>



<section class="section pt-0">
<div class="container">

<div class="cta-panel d-lg-flex align-items-center justify-content-between gap-4">

<div>

<div class="text-uppercase fw-bold opacity-75 small mb-2">
Connect with MCI
</div>

<h2 class="fw-bold mb-2">
Looking for admission, training or institutional information?
</h2>

<p class="mb-0 opacity-75">
Contact MCI Educational Group and we will guide you to the
appropriate institution or service.
</p>

</div>

<div class="mt-4 mt-lg-0">

<a href="/contact" class="btn btn-light btn-lg fw-bold">
Send Enquiry
</a>

</div>

</div>

</div>
</section>

</main>


<footer class="footer">
<div class="container">

<div class="row g-5">

<div class="col-lg-5">

<img
    src="{{ asset('images/mci-logo.png') }}"
    alt="MCI Educational Group logo"
    class="footer-logo mb-3"
    width="88"
    height="88"
>

<h5>MCI EDUCATIONAL GROUP</h5>

<p class="mb-2">
An Institution With Global Reach
</p>

<p class="mb-2">
Working under Chandrashekhar &amp; Narayan Educational Trust
</p>

<p class="mb-0">
MCI Campus, Quamruddin Ganj,<br>
Bihar Sharif, Nalanda - 803101
</p>

</div>


<div class="col-6 col-lg-3">

<h5>Explore</h5>

<div class="d-grid gap-2">

<a href="/about">About Us</a>
<a href="/institutions">Our Institutions</a>
<a href="/programs">Programs</a>
<a href="/news-events">News &amp; Events</a>
<a href="/gallery">Gallery</a>
<a href="{{ route('admin.login') }}">Admin Login</a>

</div>

</div>


<div class="col-6 col-lg-4">

<h5>Contact</h5>

<p class="mb-1">7004773247</p>
<p class="mb-1">9334779133</p>
<p class="mb-3">mcieducationalgroup@gmail.com</p>

<a href="/contact">
Contact &amp; Enquiry →
</a>

</div>

</div>


<hr class="border-secondary my-4">

<div class="small">
© {{ date('Y') }} MCI Educational Group. All rights reserved.
</div>

</div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
