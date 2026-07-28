<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — ArtiWeb</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f4f6fb; }

        /* ── Sidebar ── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: linear-gradient(175deg, #1e1b4b 0%, #4f46e5 60%, #7c3aed 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 200;
            display: flex;
            flex-direction: column;
        }
        .sidebar .sb-logo {
            padding: 1.4rem 1.5rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar .sb-logo small { color: rgba(255,255,255,.45); font-size: 10px; letter-spacing: 1px; text-transform: uppercase; }
        .sidebar .sb-section-label {
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            padding: 1.2rem 1.5rem .4rem;
            font-weight: 600;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.7);
            padding: .6rem 1rem;
            border-radius: 10px;
            margin: 2px 10px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: .6rem;
            transition: all .2s;
        }
        .sidebar .nav-link .icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.08);
            font-size: 13px;
            flex-shrink: 0;
            transition: all .2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.12);
        }
        .sidebar .nav-link.active .icon,
        .sidebar .nav-link:hover .icon {
            background: rgba(255,255,255,.2);
        }
        .sidebar .sb-footer {
            margin-top: auto;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .sidebar .sb-user {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .6rem .75rem;
            border-radius: 10px;
            background: rgba(255,255,255,.08);
        }
        .sb-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,.25);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .sb-user-info { flex: 1; min-width: 0; }
        .sb-user-info .name { font-size: 12.5px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-user-info .role { font-size: 10px; color: rgba(255,255,255,.45); }
        .btn-logout {
            background: none; border: none; padding: 0;
            color: rgba(255,255,255,.5);
            font-size: 14px;
            cursor: pointer;
            transition: color .2s;
            flex-shrink: 0;
        }
        .btn-logout:hover { color: #fff; }

        /* ── Main ── */
        .main-content { margin-left: 240px; padding: 1.75rem 2rem; }
        .topbar {
            background: #fff;
            border-radius: 14px;
            padding: .8rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h6 { font-size: 15px; font-weight: 700; color: #1e1b4b; margin: 0; }
        .topbar .breadcrumb { font-size: 12px; margin: 0; }

        /* ── Cards & components ── */
        .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .btn-primary { background: linear-gradient(135deg,#4f46e5,#7c3aed); border: none; border-radius: 8px; }
        .btn-primary:hover { opacity: .9; }
        .badge-category { background: #ede9fe; color: #6d28d9; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
        .form-control, .form-select { border-radius: 8px; border: 1.5px solid #e5e7eb; font-size: 14px; }
        .form-control:focus, .form-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .table thead th { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .5px; }
        .table tbody tr:hover { background: #f9f8ff; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sb-logo">
        <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb"
             style="height:34px; filter:brightness(0) invert(1); opacity:.9;">
        <div><small>Admin Panel</small></div>
    </div>

    <div class="sb-section-label">Management</div>
    <nav class="nav flex-column px-1">
        <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"
           href="{{ route('admin.courses.index') }}">
            <span class="icon"><i class="fas fa-book-open"></i></span>
            Courses
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
           href="{{ route('admin.users.index') }}">
            <span class="icon"><i class="fas fa-users"></i></span>
            Users
        </a>
        <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
           href="{{ route('admin.payments.index') }}">
            <span class="icon"><i class="fas fa-credit-card"></i></span>
            Payments
        </a>
    </nav>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="sb-user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">Administrator</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <h6>@yield('title', 'Dashboard')</h6>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none text-muted">Admin</a></li>
                <li class="breadcrumb-item active text-muted">@yield('title', 'Dashboard')</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0" style="border-radius:12px;">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
