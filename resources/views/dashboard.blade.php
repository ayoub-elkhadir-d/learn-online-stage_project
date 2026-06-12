<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ArtiWeb</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        :root {
            --bs-primary: #4f46e5;
            --bs-primary-rgb: 79, 70, 229;
        }
        body {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        .welcome-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%);
            color: white;
        }
        .stat-card {
            text-align: center;
            padding: 2.5rem;
        }
        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: -1;
            background-size: cover;
            background-position: center;
            transition: background-image 2s ease-in-out;
        }
        .bg-1 { background-image: url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.0.3&auto=format&fit=crop&w=2072&q=80'); }
        .bg-2 { background-image: url('https://images.unsplash.com/photo-1461749280684-dccba630e2f6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80'); }
        .bg-3 { background-image: url('https://images.unsplash.com/photo-1504639725590-34d0984388bd?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80'); }
        .bg-4 { background-image: url('https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); }
        .bg-5 { background-image: url('https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80'); }
        .bg-6 { background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2071&q=80'); }
        .bg-7 { background-image: url('https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); }
        .bg-8 { background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); }
        .bg-9 { background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2015&q=80'); }
        .bg-10 { background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); }
    </style>
</head>
<body>
    <div class="bg-overlay bg-1" id="bgOverlay"></div>
    
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" style="height:36px; object-fit:contain;">
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>{{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile') }}">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-5">
            <div class="col-12">
                <div class="dashboard-card welcome-card">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="mb-3 fw-bold">Welcome to ArtiWeb, {{ auth()->user()->name }}!</h1>
                                <p class="mb-0 opacity-75 fs-5">Ready to create amazing web experiences? Let's build something incredible together.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" style="height:60px; object-fit:contain; filter:brightness(0) invert(1); opacity:0.4;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-primary bg-opacity-10">
                            <i class="fas fa-code text-primary fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">My Projects</h5>
                        <h2 class="text-primary mb-3 fw-bold">0</h2>
                        <p class="card-text text-muted">Active projects</p>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>New Project
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i class="fas fa-paint-brush text-info fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Designs</h5>
                        <h2 class="text-info mb-3 fw-bold">0</h2>
                        <p class="card-text text-muted">Created designs</p>
                        <a href="#" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Browse
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <i class="fas fa-globe text-warning fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold">Websites</h5>
                        <h2 class="text-warning mb-3 fw-bold">0</h2>
                        <p class="card-text text-muted">Live websites</p>
                        <a href="#" class="btn btn-outline-warning">
                            <i class="fas fa-rocket me-2"></i>Deploy
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 fw-bold">
                            <i class="fas fa-history me-2 text-primary"></i>Recent Activity
                        </h5>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                            <h6 class="text-muted fw-bold">No recent activity</h6>
                            <p class="text-muted">Start working on projects to see your activity here</p>
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Start Creating
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 fw-bold">
                            <i class="fas fa-tools me-2 text-primary"></i>Quick Tools
                        </h5>
                        <div class="d-grid gap-3">
                            <a href="{{ route('profile') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user me-2"></i>Edit Profile
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-palette me-2"></i>Color Picker
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <i class="fas fa-font me-2"></i>Font Library
                            </a>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Background changer for dashboard
        const backgrounds = ['bg-1', 'bg-2', 'bg-3', 'bg-4', 'bg-5', 'bg-6', 'bg-7', 'bg-8', 'bg-9', 'bg-10'];
        let currentBg = 0;
        
        function changeBg() {
            const overlay = document.getElementById('bgOverlay');
            overlay.className = `bg-overlay ${backgrounds[currentBg]}`;
            currentBg = (currentBg + 1) % backgrounds.length;
        }
        
        // Change background every 10 seconds
        setInterval(changeBg, 10000);
    </script>
</body>
</html>