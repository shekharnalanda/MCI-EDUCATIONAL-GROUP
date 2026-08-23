<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Admin') | MCI Educational Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f5f8fb}.sidebar{min-height:100vh;background:linear-gradient(180deg,#0b4da2,#0a8f5b);color:#fff}.sidebar a{color:#eaf4ff;text-decoration:none;display:block;padding:.7rem .9rem;border-radius:.7rem}.sidebar a:hover{background:rgba(255,255,255,.12)}.card{border:0;border-radius:18px;box-shadow:0 8px 28px rgba(20,50,90,.08)}
    </style>
</head>
<body>
<div class="container-fluid"><div class="row">
    <aside class="col-lg-2 p-3 sidebar">
        <h5 class="fw-bold">MCI ADMIN</h5><hr>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.institutions.index') }}">Institutions</a>
        <a href="{{ route('admin.news.index') }}">News & Events</a>
        <a href="{{ route('admin.gallery.index') }}">Gallery</a>
        <a href="{{ route('admin.downloads.index') }}">Downloads</a>
        <a href="{{ route('admin.enquiries.index') }}">Enquiries</a>
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
