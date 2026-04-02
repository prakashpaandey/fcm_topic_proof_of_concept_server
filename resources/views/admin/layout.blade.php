<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — FCM Backend</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --surface2:  #22263a;
            --border:    #2e3248;
            --accent:    #6c63ff;
            --accent2:   #a78bfa;
            --text:      #e2e8f0;
            --muted:     #8892a4;
            --success:   #22c55e;
            --danger:    #ef4444;
            --warning:   #f59e0b;
            --sidebar-w: 240px;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh; background: var(--surface);
            border-right: 1px solid var(--border); display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 20px; border-bottom: 1px solid var(--border);
            font-size: 1.1rem; font-weight: 700; color: var(--accent2);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand i { color: var(--accent); font-size: 1.3rem; }

        .sidebar-nav { flex: 1; padding: 16px 12px; }
        .nav-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); padding: 8px 8px 4px; }

        .nav-link {
            display: flex; align-items: center; gap: 10px; padding: 10px 12px;
            border-radius: 8px; color: var(--muted); text-decoration: none;
            font-size: 0.875rem; font-weight: 500; transition: all .2s;
            margin-bottom: 2px;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(108,99,255,.15); color: var(--accent2);
        }
        .nav-link i { width: 18px; text-align: center; }

        .sidebar-footer { padding: 16px; border-top: 1px solid var(--border); }

        /* ── Main Content ── */
        .main { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; display: flex; flex-direction: column; }

        .topbar {
            background: var(--surface); border-bottom: 1px solid var(--border);
            padding: 0 28px; height: 60px; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 1rem; font-weight: 600; }
        .topbar-user { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: var(--muted); }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #fff;
        }

        .content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .card {
            background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
            overflow: hidden;
        }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            font-weight: 600; font-size: 0.9rem;
        }

        /* ── Stat Cards ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
            padding: 20px; display: flex; flex-direction: column; gap: 8px;
        }
        .stat-label { font-size: 0.75rem; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
        .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-icon { font-size: 1.4rem; margin-bottom: 4px; }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th { padding: 12px 16px; text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); border-bottom: 1px solid var(--border); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 600; letter-spacing: .3px;
        }
        .badge-success { background: rgba(34,197,94,.15); color: var(--success); }
        .badge-danger  { background: rgba(239,68,68,.15);  color: var(--danger); }
        .badge-accent  { background: rgba(108,99,255,.15); color: var(--accent2); }
        .badge-muted   { background: rgba(136,146,164,.1); color: var(--muted); }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
            border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer;
            border: none; text-decoration: none; transition: all .2s;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #5a52e0; }
        .btn-outline { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent2); }
        .btn-danger { background: rgba(239,68,68,.15); color: var(--danger); border: 1px solid rgba(239,68,68,.3); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }

        /* ── Forms ── */
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.8rem; font-weight: 500; color: var(--muted); margin-bottom: 6px; }
        input[type=text], input[type=password], input[type=url], textarea, select {
            width: 100%; background: var(--surface2); border: 1px solid var(--border);
            border-radius: 8px; padding: 10px 14px; color: var(--text); font-size: 0.875rem;
            font-family: 'Inter', sans-serif; outline: none; transition: border .2s;
        }
        input:focus, textarea:focus, select:focus { border-color: var(--accent); }
        textarea { resize: vertical; min-height: 100px; }

        .checkbox-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .checkbox-item {
            display: flex; align-items: center; gap: 8px;
            background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;
            padding: 8px 14px; cursor: pointer; transition: all .2s;
        }
        .checkbox-item:has(input:checked) { border-color: var(--accent); background: rgba(108,99,255,.1); }
        .checkbox-item input { accent-color: var(--accent); width: 15px; height: 15px; cursor: pointer; }
        .checkbox-item span { font-size: 0.85rem; }

        /* ── Alerts ── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; margin-bottom: 20px; }
        .alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: var(--success); }
        .alert-danger  { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: var(--danger); }

        .error-msg { font-size: 0.78rem; color: var(--danger); margin-top: 4px; }

        /* ── Page header ── */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-header h1 { font-size: 1.4rem; font-weight: 700; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 8px; margin-top: 24px; justify-content: center; align-items: center; }
        .pagination .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0 12px; height: 36px; min-width: 36px; border-radius: 8px; font-size: 0.85rem;
            border: 1px solid var(--border); text-decoration: none; color: var(--muted);
            background: var(--surface); transition: all .2s; cursor: pointer;
        }
        .pagination .page-link:hover:not(.disabled) { border-color: var(--accent); color: var(--accent2); background: rgba(108,99,255,.05); }
        .pagination .page-link.active { background: var(--accent); color: #fff; border-color: var(--accent); font-weight: 600; }
        .pagination .page-link.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

        /* ── Logout form ── */
        .logout-btn { background: none; border: none; cursor: pointer; color: var(--muted); font-size: 0.85rem; display: flex; align-items: center; gap: 6px; padding: 8px; border-radius: 6px; width: 100%; transition: .2s; }
        .logout-btn:hover { color: var(--danger); background: rgba(239,68,68,.1); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-bell-concierge"></i>
        FCM Backend
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        <div class="nav-label" style="margin-top:12px">Management</div>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Users
        </a>
        <a href="{{ route('admin.interests.index') }}" class="nav-link {{ request()->routeIs('admin.interests.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags"></i> Interests
        </a>
        <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
            <i class="fa-solid fa-newspaper"></i> Posts
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <div class="topbar-title">@yield('title', 'Dashboard')</div>
        <div class="topbar-user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}</div>
            <span>{{ Auth::user()->username ?? 'Admin' }}</span>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

</body>
</html>
