<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentLesson ? $currentLesson->title . ' — ' : '' }}{{ $course->title }} — Learn</title>
    @include('partials.theme-init')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/learn.js'])
    {{-- Learning-page-only accent override. Every utility class elsewhere in
         the app reads these same custom properties (bg-(--color-primary) etc.),
         so redeclaring the values here — scoped to this document only —
         reskins just this page green without touching resources/css/app.css
         or affecting the Dashboard/navbar/admin, which never load this block. --}}
    <style>
        :root {
            --color-primary: #22C55E;
            --color-primary-dark: #16A34A;
            --color-primary-active: #15803D;
            --color-primary-soft: #DCFCE7;
            --color-accent: #16A34A;
        }
        .dark {
            --color-primary: #166534;
            --color-accent: #166534;
        }
    </style>
</head>
<body class="h-screen overflow-hidden bg-(--color-bg-light) font-sans text-(--color-text) antialiased dark:bg-(--color-bg-dark) dark:text-[#ECECEC]">

<div class="flex h-full flex-col">

    {{-- Topbar --}}
    <header class="flex shrink-0 items-center gap-3 border-b border-(--color-border) bg-(--color-card) px-4 py-3 dark:border-white/10 dark:bg-(--color-card-dark)">
        <button type="button" data-sidebar-toggle aria-label="Toggle lesson list"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) hover:bg-black/5 lg:hidden dark:hover:bg-white/10">
            <x-icon name="list" class="h-4.5 w-4.5" />
        </button>

        <a href="{{ route('courses.show', $course->slug) }}" class="flex min-w-0 items-center gap-2 text-sm text-(--color-text-secondary) hover:text-(--color-text) dark:hover:text-white">
            <x-icon name="chevron-left" class="h-4 w-4 shrink-0" />
            <span class="truncate font-medium">{{ $course->title }}</span>
        </a>

        <div class="ml-auto flex items-center gap-3">
            <div class="hidden items-center gap-2 sm:flex">
                <div class="h-1.5 w-28 overflow-hidden rounded-full bg-(--color-border) dark:bg-white/10">
                    <div data-course-progress-bar class="h-full rounded-full bg-(--color-primary) transition-all" style="width:0%"></div>
                </div>
                <span data-course-progress-label class="text-xs font-medium text-(--color-text-secondary)">0%</span>
            </div>

            <button type="button" data-theme-toggle aria-label="Toggle dark mode"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) hover:bg-black/5 dark:hover:bg-white/10">
                <x-icon name="sun" class="hidden h-4.5 w-4.5 dark:block" />
                <x-icon name="moon" class="block h-4.5 w-4.5 dark:hidden" />
            </button>

            <a href="{{ route('dashboard') }}" class="hidden items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) hover:text-(--color-text) sm:inline-flex dark:hover:text-white">
                <x-icon name="layout-grid" class="h-4 w-4" />
                My Courses
            </a>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        {{-- Sidebar --}}
        <aside data-sidebar class="scrollbar-thin fixed inset-y-0 left-0 z-40 mt-[57px] w-[85%] max-w-[320px] -translate-x-full overflow-y-auto border-r border-(--color-border) bg-(--color-card) transition-transform duration-200 lg:static lg:mt-0 lg:w-80 lg:max-w-none lg:translate-x-0 lg:shrink-0 dark:border-white/10 dark:bg-(--color-card-dark)">
            <div class="sticky top-0 z-10 flex items-center gap-2 border-b border-(--color-border) bg-(--color-card) p-3 dark:border-white/10 dark:bg-(--color-card-dark)">
                <div class="relative flex-1">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                    <input type="search" data-lesson-search placeholder="Search lessons..." aria-label="Search lessons"
                           class="input-field !py-2 pl-9 text-sm">
                </div>
                <button type="button" data-sidebar-close aria-label="Close lesson list"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-(--color-text-secondary) hover:bg-black/5 lg:hidden dark:hover:bg-white/10">
                    <x-icon name="x" class="h-4.5 w-4.5" />
                </button>
            </div>

            <div class="px-3 py-2 text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">
                {{ $lessons->count() }} lesson(s)
            </div>

            <nav id="lessonSidebar" class="flex flex-col pb-4">
                @foreach($lessons as $index => $lesson)
                <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $lesson->id]) }}"
                   class="lesson-item group flex items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-black/[.03] dark:hover:bg-white/5 {{ $currentLesson && $currentLesson->id === $lesson->id ? 'bg-(--color-primary)/8 border-r-2 border-(--color-primary)' : '' }}"
                   data-lesson-nav
                   data-lesson-id="{{ $lesson->id }}"
                   data-lesson-name="{{ strtolower($lesson->title) }}">
                    <span class="relative mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold {{ $currentLesson && $currentLesson->id === $lesson->id ? 'bg-(--color-primary) text-white' : 'bg-black/5 text-(--color-text-secondary) dark:bg-white/10' }}">
                        <span data-lesson-number>{{ $index + 1 }}</span>
                        <span data-lesson-check class="absolute inset-0 hidden items-center justify-center rounded-full bg-(--color-primary) text-white">
                            <x-icon name="check" class="h-3.5 w-3.5" />
                        </span>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium {{ $currentLesson && $currentLesson->id === $lesson->id ? 'text-(--color-primary)' : '' }}">{{ $lesson->title }}</span>
                        <span class="mt-0.5 flex items-center gap-1.5 text-xs text-(--color-text-secondary)">
                            <x-icon name="clock" class="h-3 w-3 shrink-0" />
                            <span data-lesson-duration="{{ $lesson->id }}" class="tabular-nums">—:—</span>
                        </span>
                    </span>
                </a>
                @endforeach
            </nav>
        </aside>

        <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>

        {{-- Video + details --}}
        <main class="scrollbar-thin flex flex-1 flex-col overflow-y-auto" id="videoArea">
            @include('courses.partials.learn-lesson', ['course' => $course, 'lessons' => $lessons, 'currentLesson' => $currentLesson])
        </main>
    </div>
</div>

<script>
(function () {
    const videoArea = document.getElementById('videoArea');
    const sidebar = document.getElementById('lessonSidebar');
    const courseId = {{ $course->id }};
    const totalLessons = {{ $lessons->count() }};

    function activateSidebarItem(lessonId) {
        sidebar.querySelectorAll('.lesson-item').forEach(el => {
            const active = String(el.dataset.lessonId) === String(lessonId);
            el.classList.toggle('bg-(--color-primary)/8', active);
            el.classList.toggle('border-r-2', active);
            el.classList.toggle('border-(--color-primary)', active);
        });
        const active = sidebar.querySelector(`[data-lesson-id="${lessonId}"]`);
        if (active) active.scrollIntoView({ block: 'nearest' });
        closeMobileSidebar();
    }

    function updateProgress() {
        const completed = window.LessonProgress.completedCount(courseId);
        const pct = totalLessons > 0 ? Math.round((completed / totalLessons) * 100) : 0;
        document.querySelector('[data-course-progress-bar]').style.width = pct + '%';
        document.querySelector('[data-course-progress-label]').textContent = pct + '%';
    }

    function paintCompleted() {
        sidebar.querySelectorAll('[data-lesson-nav]').forEach(item => {
            if (window.LessonProgress.isCompleted(courseId, item.dataset.lessonId)) {
                item.querySelector('[data-lesson-check]').classList.remove('hidden');
                item.querySelector('[data-lesson-check]').classList.add('flex');
            }
        });
        updateProgress();
    }

    function paintDurations() {
        document.querySelectorAll('[data-lesson-duration]').forEach(el => {
            const d = window.LessonProgress.getDuration(el.dataset.lessonDuration);
            if (d) el.textContent = window.LessonProgress.formatTime(d);
        });
    }

    // A bookmark's stored `label` defaults to just the formatted timestamp
    // (see LessonProgress.addBookmark) — there's no capture UI for a real
    // custom title yet, so treat "label === the time itself" as "no title"
    // and show a readable ordinal instead of printing the time twice.
    function bookmarkTitle(bookmark, timeStr, index) {
        const label = (bookmark.label || '').trim();
        const hasCustomTitle = label.length > 0 && label !== timeStr;
        return hasCustomTitle ? label : `Bookmark ${index + 1}`;
    }

    function renderBookmarksList(lessonId) {
        const list = document.querySelector('[data-bookmarks-list]');
        const empty = document.querySelector('[data-bookmarks-empty]');
        if (!list) return;
        const bookmarks = window.LessonProgress.getBookmarks(lessonId);
        list.querySelectorAll('[data-bookmark-row]').forEach(el => el.remove());
        empty.classList.toggle('hidden', bookmarks.length > 0);
        bookmarks.forEach((b, index) => {
            const timeStr = window.LessonProgress.formatTime(b.time);

            const li = document.createElement('li');
            li.setAttribute('data-bookmark-row', '');
            li.className = 'flex items-start gap-1 rounded-xl px-1 py-1 transition-colors hover:bg-black/5 dark:hover:bg-white/5';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'flex min-w-0 flex-1 items-start gap-2.5 rounded-lg px-1.5 py-1.5 text-left';

            const badge = document.createElement('span');
            badge.className = 'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)';
            badge.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>';

            const textWrap = document.createElement('span');
            textWrap.className = 'min-w-0 flex-1';

            const timeEl = document.createElement('span');
            timeEl.className = 'block text-xs font-semibold tabular-nums text-(--color-primary)';
            timeEl.textContent = timeStr;

            const titleEl = document.createElement('span');
            titleEl.className = 'mt-0.5 block truncate text-sm text-(--color-text) dark:text-white/90';
            titleEl.textContent = bookmarkTitle(b, timeStr, index);

            textWrap.append(timeEl, titleEl);
            btn.append(badge, textWrap);
            btn.addEventListener('click', () => window.LessonPlayerController.seekTo(b.time));
            li.appendChild(btn);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.setAttribute('aria-label', 'Remove bookmark');
            removeBtn.className = 'mt-0.5 shrink-0 rounded-lg p-1.5 text-(--color-text-secondary) transition-colors hover:bg-black/10 hover:text-(--color-danger) dark:hover:bg-white/10';
            removeBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                window.LessonProgress.removeBookmark(lessonId, b.createdAt);
                renderBookmarksList(lessonId);
                window.LessonPlayerController.refreshBookmarks();
            });
            li.appendChild(removeBtn);

            list.appendChild(li);
        });
    }

    function bindNotes(lessonId) {
        const field = document.querySelector('[data-notes-field]');
        const status = document.querySelector('[data-notes-status]');
        if (!field) return;
        field.value = window.LessonProgress.getNote(lessonId);
        let timer;
        field.addEventListener('input', () => {
            status.textContent = 'Saving...';
            clearTimeout(timer);
            timer = setTimeout(() => {
                window.LessonProgress.saveNote(lessonId, field.value);
                status.textContent = 'Saved';
                setTimeout(() => { if (status.textContent === 'Saved') status.textContent = ''; }, 1500);
            }, 600);
        });
    }

    function showLoading(show) {
        videoArea.style.opacity = show ? '0.5' : '1';
    }

    function loadLesson(url, lessonId, push) {
        showLoading(true);
        window.LessonPlayerController.destroy();

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
                initLessonView();
            })
            .catch(() => { window.location.href = url; })
            .finally(() => showLoading(false));
    }

    function initLessonView() {
        const player = videoArea.querySelector('[data-secure-player]');
        if (player) {
            window.LessonPlayerController.mount(player);
            bindNotes(player.dataset.lessonId);
            renderBookmarksList(player.dataset.lessonId);
        }
        paintCompleted();
        paintDurations();
    }

    document.addEventListener('click', function (e) {
        const link = e.target.closest('[data-lesson-nav]');
        if (!link) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
        e.preventDefault();
        loadLesson(link.href, link.dataset.lessonId, true);
    });

    window.addEventListener('popstate', function (e) {
        loadLesson(window.location.href, e.state ? e.state.lessonId : null, false);
    });

    document.addEventListener('lesson:duration-known', paintDurations);
    document.addEventListener('lesson:bookmarks-changed', function (e) {
        renderBookmarksList(e.detail.lessonId);
    });

    document.querySelector('[data-lesson-search]').addEventListener('input', function (e) {
        const q = e.target.value.trim().toLowerCase();
        sidebar.querySelectorAll('[data-lesson-nav]').forEach(item => {
            item.classList.toggle('hidden', q.length > 0 && !item.dataset.lessonName.includes(q));
        });
    });

    function closeMobileSidebar() {
        document.querySelector('[data-sidebar]').classList.add('-translate-x-full');
        document.querySelector('[data-sidebar-backdrop]').classList.add('hidden');
    }

    document.querySelector('[data-sidebar-toggle]').addEventListener('click', function () {
        document.querySelector('[data-sidebar]').classList.toggle('-translate-x-full');
        document.querySelector('[data-sidebar-backdrop]').classList.toggle('hidden');
    });
    document.querySelector('[data-sidebar-backdrop]').addEventListener('click', closeMobileSidebar);
    document.querySelector('[data-sidebar-close]').addEventListener('click', closeMobileSidebar);

    // On the very first page load this inline script runs synchronously
    // during HTML parsing, BEFORE the Vite `type="module"` bundles (which
    // are deferred per spec) have executed — so window.LessonPlayerController
    // / window.LessonProgress don't exist yet at this point. Calling
    // initLessonView() immediately would throw silently and the player would
    // never mount, leaving only the poster visible. Wait for DOMContentLoaded
    // (which always fires after deferred/module scripts finish) unless the
    // globals are already there.
    if (window.LessonPlayerController && window.LessonProgress) {
        initLessonView();
    } else {
        document.addEventListener('DOMContentLoaded', initLessonView, { once: true });
    }
})();
</script>
</body>
</html>
