<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — FCM Backend</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%; max-width: 420px; padding: 20px;
        }
        .login-brand {
            text-align: center; margin-bottom: 32px;
        }
        .login-brand .icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, #6c63ff, #a78bfa);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px; font-size: 1.5rem; color: #fff;
        }
        .login-brand h1 { font-size: 1.4rem; font-weight: 700; }
        .login-brand p  { color: #8892a4; font-size: 0.85rem; margin-top: 4px; }

        .card {
            background: #1a1d27; border: 1px solid #2e3248;
            border-radius: 16px; padding: 32px;
        }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.8rem; font-weight: 500; color: #8892a4; margin-bottom: 6px; }
        input {
            width: 100%; background: #22263a; border: 1px solid #2e3248;
            border-radius: 8px; padding: 11px 14px; color: #e2e8f0;
            font-size: 0.875rem; font-family: 'Inter', sans-serif; outline: none; transition: border .2s;
        }
        input:focus { border-color: #6c63ff; }
        .btn-primary {
            width: 100%; background: #6c63ff; color: #fff; border: none;
            border-radius: 8px; padding: 12px; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary:hover { background: #5a52e0; }
        .error-box {
            background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3);
            border-radius: 8px; padding: 10px 14px; font-size: 0.85rem;
            color: #ef4444; margin-bottom: 18px;
        }
        .remember { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 0.85rem; color: #8892a4; }
        .remember input { width: auto; accent-color: #6c63ff; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-brand">
        <div class="icon"><i class="fa-solid fa-bell-concierge"></i></div>
        <h1>FCM Backend</h1>
        <p>Admin Dashboard — Sign in to continue</p>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="error-box">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username" autofocus required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="••••••••" required>
            </div>
            <label class="remember">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In
            </button>
        </form>
    </div>
</div>
</body>
</html>
