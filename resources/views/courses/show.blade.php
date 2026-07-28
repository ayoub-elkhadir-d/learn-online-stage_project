<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} — ArtiWeb</title>
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
            padding: 3rem 0;
        }
        .card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .badge-cat { background: rgba(255,255,255,.2); color: #fff; font-size:12px; font-weight:600; padding:4px 14px; border-radius:20px; }
        .price-big { font-size:2rem; font-weight:700; color:#4f46e5; }
        .cover-img { width:100%; max-height:320px; object-fit:cover; border-radius:16px; }
        .cover-placeholder {
            width:100%; height:240px;
            background: linear-gradient(135deg,#ede9fe,#ddd6fe);
            border-radius:16px;
            display:flex; align-items:center; justify-content:center;
        }
        .btn-purchase {
            background: linear-gradient(135deg,#4f46e5,#7c3aed);
            border: none; border-radius: 10px;
            padding: .8rem; font-weight: 600; font-size: 15px;
            color: #fff; width: 100%;
            transition: all .2s;
        }
        .btn-purchase:hover { opacity:.9; box-shadow: 0 6px 18px rgba(79,70,229,.35); color:#fff; }
        .feature-list li { padding: .4rem 0; font-size: 13px; color: #6b7280; }
        .feature-list li i { color: #10b981; width: 18px; }
    </style>
</head>
<body>

@include('partials.navbar')

<div class="hero">
    <div class="container">
        <a href="{{ route('courses.index') }}" class="text-white-50 text-decoration-none d-inline-flex align-items-center gap-1 mb-3" style="font-size:13px;">
            <i class="fas fa-arrow-left"></i> Back to courses
        </a>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <h2 class="fw-bold mb-1">{{ $course->title }}</h2>
                <span class="badge-cat">{{ $course->category->name }}</span>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">

    @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show border-0" style="border-radius:12px;">
            <i class="fas fa-info-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            @if($course->cover_image_path)
                <img src="{{ Storage::url($course->cover_image_path) }}" class="cover-img mb-4" alt="{{ $course->title }}">
            @else
                <div class="cover-placeholder mb-4">
                    <i class="fas fa-graduation-cap fa-4x" style="color:#a78bfa;"></i>
                </div>
            @endif

            <div class="card p-4">
                <h5 class="fw-bold mb-3">About this course</h5>
                <p style="line-height:1.85; color:#4b5563;">{{ $course->description ?: 'No description available.' }}</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4" style="position:sticky;top:80px;">
                <div class="text-center mb-4">
                    <div class="price-big">{{ $course->price_mad }} <span style="font-size:1rem;color:#9ca3af;">MAD</span></div>
                    <small class="text-muted">One-time payment — lifetime access</small>
                </div>

                @auth
                    @if($purchase && $purchase->status === 'paid')
                        <div class="text-center mb-4">
                            <div style="width:60px;height:60px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;">
                                <i class="fas fa-check" style="color:#065f46;font-size:1.4rem;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">You have access!</h6>
                            <p class="text-muted" style="font-size:12px;">You purchased this course on {{ $purchase->purchased_at->format('d M Y') }}</p>
                            <a href="{{ route('courses.learn', $course->slug) }}" class="btn-purchase d-block text-center text-decoration-none">
                                <i class="fas fa-play me-2"></i>Start Learning
                            </a>
                        </div>
                    @elseif($purchase && $purchase->status === 'pending')
                        <div class="text-center mb-4">
                            <div style="width:60px;height:60px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;">
                                <i class="fas fa-clock" style="color:#92400e;font-size:1.4rem;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Payment Pending</h6>
                            <p class="text-muted" style="font-size:12px;">Your payment is awaiting admin confirmation.</p>
                            <div class="rounded-3 px-3 py-2" style="background:#fef3c7;font-size:12px;color:#92400e;">
                                <i class="fas fa-info-circle me-1"></i>Reference: <strong>{{ $purchase->reference ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <div style="width:60px;height:60px;border-radius:50%;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;">
                                <i class="fas fa-shopping-cart" style="color:#4f46e5;font-size:1.4rem;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Ready to purchase?</h6>
                            <p class="text-muted" style="font-size:12px;">Complete the secure checkout form to submit your payment proof.</p>
                            <a href="{{ route('courses.checkout', $course->slug) }}" class="btn-purchase d-block text-center text-decoration-none mb-3">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                            <small class="text-muted" style="font-size:10px;">
                                <i class="fas fa-lock me-1" style="color:#4f46e5;"></i>
                                Secure payment — Admin confirmation required
                            </small>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-purchase d-block text-center text-decoration-none mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Purchase
                    </a>
                @endauth

                <ul class="feature-list list-unstyled mb-0">
                    <li><i class="fas fa-check-circle me-2"></i>Full course access</li>
                    <li><i class="fas fa-check-circle me-2"></i>Certificate upon completion</li>
                    <li><i class="fas fa-check-circle me-2"></i>Payment confirmed by admin</li>
                    <li><i class="fas fa-check-circle me-2"></i>Lifetime access</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
