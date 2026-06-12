<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ArtiWeb')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #7774b1 0%, #7c3aed 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1471&q=80') center/cover;
            opacity: 0.8; /* Slightly reduced opacity to blend beautifully with the gradient */
            filter: blur(10px); /* This adds the blur effect */
            transform: scale(1.05); /* Slightly zooms in to prevent bleeding white edges from the blur */
            z-index: -1;
        }
        .auth-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 1rem 1.25rem;
            text-align: center;
        }
        .auth-body {
            padding: 1.25rem 1.5rem;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 8px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .brand-logo {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .auth-link {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-link:hover {
            color: #7c3aed;
            text-decoration: underline;
        }
        .divider {
            position: relative;
            text-align: center;
            margin: 1.5rem 0;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="auth-card">
                        <div class="auth-header">
                            <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" style="height:50px; object-fit:contain; filter:brightness(0) invert(1);" class="mb-1">
                            <br>
                            <small class="opacity-75">@yield('subtitle', 'Creative Web Platform')</small>
                        </div>
                        <div class="auth-body">
                            @if(session('success'))
                                <div class="alert alert-success d-flex align-items-center mb-4">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(session('status'))
                                <div class="alert alert-info d-flex align-items-center mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ session('status') }}
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Please fix the following errors:</strong>
                                    </div>
                                    <ul class="mb-0 ps-4">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>