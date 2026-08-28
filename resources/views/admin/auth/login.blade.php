<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | MCI Educational Group</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mci-logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;display:flex;align-items:center;background:linear-gradient(135deg,#0b4da2,#0a8f5b)}
        .login-card{max-width:430px;margin:auto;border:0;border-radius:22px;box-shadow:0 18px 55px rgba(0,0,0,.2)}
        .brand{font-weight:800;color:#0b4da2}.admin-logo{width:120px;height:120px;object-fit:contain;margin-bottom:12px}
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <img src="{{ asset('images/mci-logo.png') }}" alt="MCI Educational Group logo" class="admin-logo">
            <h2 class="brand mb-1">MCI EDUCATIONAL GROUP</h2>
            <div class="text-muted">Secure Admin Login</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" required>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-primary btn-lg w-100">Login</button>
        </form>
        <div class="text-center mt-4"><a href="{{ route('home') }}" class="text-decoration-none">← Back to website</a></div>
    </div>
</div>
</body>
</html>
