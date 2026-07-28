<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses — ArtiWeb</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.navbar-styles')
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f8f7ff; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            position: relative;
            overflow: hidden;
            padding: 5rem 0 4rem;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 50%, rgba(79,70,229,.45) 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 80% 30%, rgba(124,58,237,.4) 0%, transparent 60%);
        }
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .hero-content { position: relative; z-index: 2; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            color: rgba(255,255,255,.85);
            font-size: 12px; font-weight: 600;
            padding: .35rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(8px);
            margin-bottom: 1.25rem;
            letter-spacing: .5px;
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 1rem;
        }
        .hero h1 span {
            background: linear-gradient(90deg, #a78bfa, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p { color: rgba(255,255,255,.65); font-size: 15px; line-height: 1.7; max-width: 480px; }
        .hero-stat { text-align: center; }
        .hero-stat .num { font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1; }
        .hero-stat .lbl { font-size: 11px; color: rgba(255,255,255,.5); margin-top: 2px; text-transform: uppercase; letter-spacing: .5px; }
        .hero-stat-divider { width: 1px; background: rgba(255,255,255,.12); }

        /* ── FILTER ── */
        .filter-section { background: #fff; border-bottom: 1px solid #f0eeff; }
        .filter-pill {
            font-size: 13px; font-weight: 500;
            padding: .45rem 1.1rem;
            border-radius: 50px;
            border: 1.5px solid #e8e5f8;
            color: #6b7280;
            background: #fff;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: .4rem;
            transition: all .2s;
        }
        .filter-pill:hover { border-color: #4f46e5; color: #4f46e5; background: rgba(79,70,229,.04); }
        .filter-pill.active {
            background: linear-gradient(135deg,#4f46e5,#7c3aed);
            color: #fff; border-color: transparent;
            box-shadow: 0 3px 10px rgba(79,70,229,.3);
        }

        /* ── COURSE CARD ── */
        .course-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(79,70,229,.07);
            overflow: hidden;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .course-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg,rgba(79,70,229,.04),rgba(124,58,237,.04));
            opacity: 0;
            transition: opacity .3s;
            z-index: 0;
        }
        .course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(79,70,229,.14), 0 0 0 1px rgba(79,70,229,.08);
        }
        .course-card:hover::before { opacity: 1; }

        /* Cover */
        .card-cover {
            height: 180px;
            position: relative;
            overflow: hidden;
        }
        .card-cover img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }
        .course-card:hover .card-cover img { transform: scale(1.06); }
        .card-cover-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: .5rem;
        }
        .cover-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 60%;
            background: linear-gradient(to top, rgba(15,12,41,.5), transparent);
        }
        .cover-cat-badge {
            position: absolute;
            top: 12px; left: 14px;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(8px);
            color: #4f46e5;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 50px;
            letter-spacing: .3px;
        }
        .cover-price {
            position: absolute;
            bottom: 12px; right: 14px;
            background: linear-gradient(135deg,#4f46e5,#7c3aed);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
        }

        /* Card body */
        .card-body-inner { padding: 1.25rem 1.25rem 1.35rem; position: relative; z-index: 1; }
        .card-title-text { font-size: 15px; font-weight: 700; color: #111827; line-height: 1.4; margin-bottom: .5rem; }
        .card-desc { font-size: 12.5px; color: #9ca3af; line-height: 1.6; margin-bottom: 1rem; }
        .btn-view {
            width: 100%;
            padding: .6rem;
            border-radius: 10px;
            background: linear-gradient(135deg,#4f46e5,#7c3aed);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            border: none;
            text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: .4rem;
            transition: all .2s;
        }
        .btn-view:hover { opacity: .9; color: #fff; box-shadow: 0 5px 16px rgba(79,70,229,.35); transform: translateY(-1px); }

        /* ── SECTION TITLE ── */
        .section-header h5 { font-size: 20px; font-weight: 700; color: #111827; }
        .section-header .count-badge {
            background: #ede9fe; color: #6d28d9;
            font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 20px;
        }
    </style>
</head>
<body>

@include('partials.navbar')

{{-- ── HERO ── --}}
<div class="hero">
    <div class="hero-grid"></div>
    <div class="container hero-content">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="fas fa-bolt" style="color:#a78bfa;"></i>
                    Professional Training Courses
                </div>
                <h1>
                    Learn new skills<br>
                    <span>grow your career</span>
                </h1>
                <p class="mb-4">
                    Expert-led courses designed to help you master in-demand skills.
                    One-time payment, lifetime access.
                </p>
                <div class="d-flex align-items-center gap-4">
                    <div class="hero-stat">
                        <div class="num">250</div>
                        <div class="lbl">MAD / course</div>
                    </div>
                    <div class="hero-stat-divider" style="height:36px;"></div>
                    <div class="hero-stat">
                        <div class="num">4</div>
                        <div class="lbl">Categories</div>
                    </div>
                    <div class="hero-stat-divider" style="height:36px;"></div>
                    <div class="hero-stat">
                        <div class="num">∞</div>
                        <div class="lbl">Lifetime Access</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div style="position:relative;width:220px;height:220px;">
                    <div style="position:absolute;inset:0;border-radius:50%;background:rgba(79,70,229,.2);filter:blur(40px);"></div>
                    <div style="position:relative;width:100%;height:100%;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-graduation-cap" style="font-size:5rem;color:rgba(167,139,250,.7);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<div class="filter-section py-3 sticky-top" style="top:61px;z-index:99;">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted me-1" style="font-size:12px;font-weight:600;">FILTER:</span>
            <a href="{{ route('courses.index') }}"
               class="filter-pill {{ !request('category') ? 'active' : '' }}">
                <i class="fas fa-border-all" style="font-size:10px;"></i> All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                   class="filter-pill {{ request('category') == $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── COURSES GRID ── --}}
<div class="container py-5">

    @if($courses->isEmpty())
        <div class="text-center py-5">
            <div style="width:80px;height:80px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <i class="fas fa-search" style="color:#7c3aed;font-size:1.5rem;"></i>
            </div>
            <h6 class="fw-bold text-dark">No courses found</h6>
            <p class="text-muted" style="font-size:13px;">Try a different category or check back later.</p>
        </div>
    @else
        <div class="section-header d-flex align-items-center gap-3 mb-4">
            <h5 class="mb-0">Available Courses</h5>
            <span class="count-badge">{{ $courses->total() }} courses</span>
        </div>

        <div class="row g-4">
            @foreach($courses as $course)
            <div class="col-xl-4 col-md-6">
                <div class="course-card h-100 d-flex flex-column">
                    <div class="card-cover">
                        @if($course->cover_image_path)
                            <img src="{{ Storage::url($course->cover_image_path) }}" alt="{{ $course->title }}">
                            <div class="cover-overlay"></div>
                        @else
                            <div class="card-cover-placeholder" style="background:linear-gradient(135deg,#1e1b4b,#4338ca);">
                                <i class="fas fa-graduation-cap" style="font-size:2.8rem;color:rgba(167,139,250,.6);"></i>
                                <span style="font-size:11px;color:rgba(255,255,255,.3);font-weight:500;">No image</span>
                            </div>
                        @endif
                        <span class="cover-cat-badge">{{ $course->category->name }}</span>
                        <span class="cover-price">{{ $course->price_mad }} MAD</span>
                    </div>

                    <div class="card-body-inner d-flex flex-column flex-grow-1">
                        <div class="card-title-text">{{ $course->title }}</div>
                        <div class="card-desc flex-grow-1">{{ Str::limit($course->description, 90) }}</div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="btn-view">
                            View Course <i class="fas fa-arrow-right" style="font-size:11px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-5">{{ $courses->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
