<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentLesson ? $currentLesson->title . ' — ' : '' }}{{ $course->title }} — Learn</title>
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
            transition: all .15s;
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
            position: relative;
        }
        .video-wrapper {
            background: #000;
            width: 100%;
            max-height: 65vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .video-wrapper video {
            width: 100%;
            max-height: 65vh;
            outline: none;
        }
        .video-wrapper {
            user-select: none;
            -webkit-user-select: none;
        }
        .video-wrapper video {
            -webkit-user-drag: none;
        }
        .video-loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s;
        }
        .video-loading-overlay.show { opacity: 1; pointer-events: all; }
        .spinner-ring {
            width: 42px; height: 42px;
            border-radius: 50%;
            border: 3px solid rgba(124,58,237,.25);
            border-top-color: #a78bfa;
            animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

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
            .learn-layout { flex-direction: column; height: auto; }
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
    <div class="lesson-sidebar" id="lessonSidebar">
        <div class="sidebar-header">Course Lessons · {{ $lessons->count() }} total</div>

        @foreach($lessons as $index => $lesson)
        <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $lesson->id]) }}"
           class="lesson-item {{ $currentLesson && $currentLesson->id === $lesson->id ? 'active' : '' }}"
           data-lesson-nav
           data-lesson-id="{{ $lesson->id }}">
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
    <div class="video-area" id="videoArea">
        @include('courses.partials.learn-lesson', ['course' => $course, 'lessons' => $lessons, 'currentLesson' => $currentLesson])
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/secure-player.js') }}"></script>
<script>
(function () {
    const videoArea = document.getElementById('videoArea');
    const sidebar = document.getElementById('lessonSidebar');

    function activateSidebarItem(lessonId) {
        sidebar.querySelectorAll('.lesson-item').forEach(el => {
            el.classList.toggle('active', String(el.dataset.lessonId) === String(lessonId));
        });
        const active = sidebar.querySelector('.lesson-item.active');
        if (active) active.scrollIntoView({ block: 'nearest' });
    }

    function showLoading(show) {
        const overlay = videoArea.querySelector('.video-loading-overlay');
        if (overlay) overlay.classList.toggle('show', show);
    }

    function loadLesson(url, lessonId, push) {
        showLoading(true);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newArea = doc.getElementById('videoArea');
                if (!newArea) { window.location.href = url; return; }

                videoArea.innerHTML = newArea.innerHTML;
                document.title = doc.title;
                activateSidebarItem(lessonId);

                if (push) history.pushState({ lessonId }, '', url);
                bindVideoEvents(true);
            })
            .catch(() => { window.location.href = url; })
            .finally(() => showLoading(false));
    }

    function bindVideoEvents(isSwap) {
        const video = videoArea.querySelector('video');
        if (!video) return;

        if (isSwap) {
            // Video/source tags inserted via innerHTML don't reliably start
            // loading on their own — force it, then attempt to play.
            video.load();
            const playPromise = video.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(() => {
                    // Autoplay was blocked (no user gesture in scope) — that's fine,
                    // the visible controls let the learner press play manually.
                });
            }
        }

        video.addEventListener('ended', function () {
            const nextBtn = videoArea.querySelector('[data-next-lesson]');
            if (nextBtn) loadLesson(nextBtn.href, nextBtn.dataset.lessonId, true);
        });
    }

    document.addEventListener('click', function (e) {
        const link = e.target.closest('[data-lesson-nav]');
        if (!link) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return; // let modified clicks open normally
        e.preventDefault();
        loadLesson(link.href, link.dataset.lessonId, true);
    });

    window.addEventListener('popstate', function (e) {
        loadLesson(window.location.href, e.state ? e.state.lessonId : null, false);
    });

    bindVideoEvents();
})();
</script>
</body>
</html>
