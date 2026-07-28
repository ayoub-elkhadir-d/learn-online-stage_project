<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} — Learn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.navbar-styles')
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #0f0e17; color: #fff; }

        .topbar {
            background: #1a1829;
            border-bottom: 1px solid rgba(255,255,255,.07);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar .course-title { font-size: 14px; font-weight: 600; color: #e0e0ff; }
        .topbar .back-link { color: rgba(255,255,255,.5); font-size: 13px; text-decoration: none; }
        .topbar .back-link:hover { color: #fff; }

        .learn-layout { display: flex; height: calc(100vh - 55px); }

        /* Sidebar */
        .lesson-sidebar {
            width: 300px;
            min-width: 300px;
            background: #1a1829;
            border-right: 1px solid rgba(255,255,255,.07);
            overflow-y: auto;
            flex-shrink: 0;
        }
        .sidebar-header {
            padding: 1rem 1.25rem .75rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
        }
        .lesson-item {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .85rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.04);
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
        }
        .lesson-item:hover { background: rgba(255,255,255,.05); }
        .lesson-item.active { background: rgba(79,70,229,.2); border-left: 3px solid #7c3aed; }
        .lesson-item .num {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            color: rgba(255,255,255,.5);
            font-size: 11px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .lesson-item.active .num { background: #7c3aed; color: #fff; }
        .lesson-item .lesson-info { flex: 1; min-width: 0; }
        .lesson-item .lesson-info .lesson-name { font-size: 13px; font-weight: 500; color: #e0e0ff; line-height: 1.3; }
        .lesson-item .lesson-info .lesson-desc { font-size: 11px; color: rgba(255,255,255,.35); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Video area */
        .video-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: #0f0e17;
        }
        .video-wrapper {
            background: #000;
            width: 100%;
            max-height: 65vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .video-wrapper video {
            width: 100%;
            max-height: 65vh;
            outline: none;
        }
        .lesson-detail {
            padding: 1.75rem 2rem;
            max-width: 800px;
        }
        .lesson-detail h4 { font-weight: 700; color: #e0e0ff; }
        .lesson-detail p { color: rgba(255,255,255,.55); font-size: 14px; line-height: 1.8; }

        .no-lessons {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,.3);
            text-align: center;
            padding: 3rem;
        }

        @media (max-width: 768px) {
            .learn-layout { flex-direction: column; }
            .lesson-sidebar { width: 100%; min-width: unset; height: 220px; border-right: none; border-bottom: 1px solid rgba(255,255,255,.07); }
        }
    </style>
</head>
<body>

<div class="topbar">
    <a href="{{ route('courses.show', $course->slug) }}" class="back-link">
        <i class="fas fa-arrow-left me-2"></i>{{ $course->title }}
    </a>
    <div class="course-title d-none d-md-block">
        <i class="fas fa-play-circle me-2" style="color:#7c3aed;"></i>Learning Mode
    </div>
    <a href="{{ route('dashboard') }}" class="back-link">
        <i class="fas fa-th-large me-1"></i>My Courses
    </a>
</div>

<div class="learn-layout">

    {{-- Lesson sidebar --}}
    <div class="lesson-sidebar">
        <div class="sidebar-header">Course Lessons · {{ $lessons->count() }} total</div>

        @foreach($lessons as $index => $lesson)
        <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $lesson->id]) }}"
           class="lesson-item {{ $currentLesson && $currentLesson->id === $lesson->id ? 'active' : '' }}">
            <div class="num">{{ $index + 1 }}</div>
            <div class="lesson-info">
                <div class="lesson-name">{{ $lesson->title }}</div>
                @if($lesson->description)
                <div class="lesson-desc">{{ $lesson->description }}</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    {{-- Video + details --}}
    <div class="video-area">
        @if($currentLesson)
        <div class="video-wrapper">
            <video controls autoplay controlsList="nodownload" oncontextmenu="return false;">
                <source src="{{ Storage::url($currentLesson->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="lesson-detail">
            <h4>{{ $currentLesson->title }}</h4>
            @if($currentLesson->description)
            <p>{{ $currentLesson->description }}</p>
            @else
            <p class="fst-italic">No description provided for this lesson.</p>
            @endif

            {{-- Prev / Next navigation --}}
            @php
                $ids = $lessons->pluck('id')->toArray();
                $pos = array_search($currentLesson->id, $ids);
                $prevLesson = $pos > 0 ? $lessons[$pos - 1] : null;
                $nextLesson = $pos < count($ids) - 1 ? $lessons[$pos + 1] : null;
            @endphp
            <div class="d-flex gap-3 mt-4">
                @if($prevLesson)
                <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $prevLesson->id]) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-chevron-left me-1"></i>Previous
                </a>
                @endif
                @if($nextLesson)
                <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $nextLesson->id]) }}"
                   class="btn btn-sm text-white" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;border-radius:8px;">
                    Next <i class="fas fa-chevron-right ms-1"></i>
                </a>
                @else
                <div class="rounded-3 px-3 py-2 d-flex align-items-center gap-2"
                     style="background:rgba(16,185,129,.15);color:#6ee7b7;font-size:13px;">
                    <i class="fas fa-check-circle"></i> You've reached the last lesson!
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="no-lessons">
            <i class="fas fa-video fa-3x mb-3"></i>
            <h6>No lessons available yet</h6>
            <p style="font-size:13px;">The instructor hasn't added any lessons to this course yet. Check back later.</p>
        </div>
        @endif
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
