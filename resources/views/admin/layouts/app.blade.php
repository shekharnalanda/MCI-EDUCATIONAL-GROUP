<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Admin') | MCI Educational Group</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mci-logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f5f8fb}.sidebar{min-height:100vh;background:linear-gradient(180deg,#0b4da2,#0a8f5b);color:#fff}.sidebar a{color:#eaf4ff;text-decoration:none;display:block;padding:.65rem .85rem;border-radius:.7rem}.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.14)}.card{border:0;border-radius:18px;box-shadow:0 8px 28px rgba(20,50,90,.08)}.admin-brand{text-align:center}.admin-brand img{width:105px;height:105px;object-fit:contain;background:#fff;border-radius:18px;padding:5px;margin-bottom:10px}.nav-section{font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.65);margin:1rem .9rem .35rem}
    </style>
</head>
<body>
<div class="container-fluid"><div class="row">
    <aside class="col-lg-2 p-3 sidebar">
        <div class="admin-brand"><img src="{{ asset('images/mci-logo.png') }}" alt="MCI Educational Group logo"><h5 class="fw-bold mb-1">MCI MASTER ADMIN</h5><small>Educational Group</small></div><hr>
        <a class="{{ request()->routeIs('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
        <div class="nav-section">Central Management</div>
        <a class="{{ request()->routeIs('admin.enquiries.*')?'active':'' }}" href="{{ route('admin.enquiries.index') }}">Central Enquiries</a>
        <a class="{{ request()->routeIs('admin.admissions.*')?'active':'' }}" href="{{ route('admin.admissions.index') }}">Admissions</a>
        <a class="{{ request()->routeIs('admin.customers.*')?'active':'' }}" href="{{ route('admin.customers.index') }}">Customers</a>
        <a class="{{ request()->routeIs('admin.follow-ups.*')?'active':'' }}" href="{{ route('admin.follow-ups.index') }}">Follow-ups</a>
        <a class="{{ request()->routeIs('admin.communications.*')?'active':'' }}" href="{{ route('admin.communications.index') }}">Communication History</a>
        <a class="{{ request()->routeIs('admin.auto-replies.*')?'active':'' }}" href="{{ route('admin.auto-replies.index') }}">Auto Reply Center</a>
        <a class="{{ request()->routeIs('admin.institutions.*')?'active':'' }}" href="{{ route('admin.institutions.index') }}">Business Units</a>
        <div class="nav-section">Website CMS</div>
        <a href="{{ route('admin.news.index') }}">News & Events</a>
        <a href="{{ route('admin.gallery.index') }}">Gallery</a>
        <a href="{{ route('admin.downloads.index') }}">Downloads</a>
        <a href="{{ route('admin.settings.index') }}">Settings</a>
        <hr><a href="{{ route('home') }}" target="_blank">View Website</a>
        <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">@csrf<button class="btn btn-light w-100">Logout</button></form>
    </aside>
    <main class="col-lg-10 p-4 p-md-5">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div></div>
</body></html>
