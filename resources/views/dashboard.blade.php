<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses — ArtiWeb</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.navbar-styles')
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f4f6fb; }
        .hero {
            background: linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);
            color: white;
            padding: 2.5rem 0;
        }
        .card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .course-card { overflow: hidden; transition: all .25s; }
        .course-card:hover { transform: translateY(-4px); box-shadow: 0 14px 32px rgba(79,70,229,.12); }
        .cover {
            height: 145px;
            background: linear-gradient(135deg,#ede9fe,#ddd6fe);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .cover img { width:100%; height:100%; object-fit:cover; }
        .badge-paid    { background:#d1fae5; color:#065f46; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; }
        .badge-pending { background:#fef3c7; color:#92400e; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; }
        .stat-box { background:#fff; border-radius:16px; padding:1.25rem 1.5rem; box-shadow:0 2px 12px rgba(0,0,0,.07); }
        .stat-box .num { font-size:1.8rem; font-weight:700; color:#4f46e5; }
        .stat-box .label { font-size:12px; color:#9ca3af; margin-top:2px; }
        .btn-primary { background: linear-gradient(135deg,#4f46e5,#7c3aed); border:none; border-radius:8px; }
    </style>
</head>
<body>

@include('partials.navbar')

<div class="hero">
    <div class="container">
        <h3 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }} 👋</h3>
        <p class="opacity-75 mb-0">Track your purchased courses and continue learning.</p>
    </div>
</div>

<div class="container py-4">

    @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show border-0" style="border-radius:12px;">
            <i class="fas fa-info-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="num">{{ $purchases->total() }}</div>
                <div class="label">Total Purchases</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="num">{{ $purchases->getCollection()->where('status','paid')->count() }}</div>
                <div class="label">Active Courses</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <div class="num">{{ $purchases->getCollection()->where('status','pending')->count() }}</div>
                <div class="label">Pending Payment</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">My Courses</h5>
        <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-plus me-1"></i>Browse More
        </a>
    </div>

    @if($purchases->isEmpty())
        <div class="card p-5 text-center">
            <i class="fas fa-graduation-cap fa-3x mb-3 d-block" style="color:#a78bfa;"></i>
            <h6 class="fw-bold text-dark">No courses yet</h6>
            <p class="text-muted mb-3">Start your learning journey today.</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm mx-auto px-4" style="width:fit-content;">
                Browse Courses
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($purchases as $purchase)
            <div class="col-lg-4 col-md-6">
                <div class="card course-card h-100">
                    <div class="cover">
                        @if($purchase->course->cover_image_path)
                            <img src="{{ Storage::url($purchase->course->cover_image_path) }}" alt="{{ $purchase->course->title }}">
                        @else
                            <i class="fas fa-graduation-cap fa-3x" style="color:#a78bfa;"></i>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $purchase->course->title }}</h6>
                            @if($purchase->status === 'paid')
                                <span class="badge-paid ms-2 flex-shrink-0"><i class="fas fa-check me-1"></i>Active</span>
                            @else
                                <span class="badge-pending ms-2 flex-shrink-0"><i class="fas fa-clock me-1"></i>Pending</span>
                            @endif
                        </div>
                        <div class="text-muted mb-3" style="font-size:12px;">
                            {{ $purchase->course->category->name }} · {{ $purchase->purchased_at->format('d M Y') }}
                        </div>

                        @if($purchase->status === 'paid')
                            <a href="{{ route('courses.learn', $purchase->course->slug) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-play me-1"></i>Start Learning
                            </a>
                        @else
                            <div class="rounded-3 px-3 py-2" style="background:#fef3c7;font-size:12px;color:#92400e;">
                                <i class="fas fa-info-circle me-1"></i>Waiting for admin payment confirmation.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $purchases->links() }}</div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
