@extends('layouts.site')

@section('title', 'My Courses — ArtiWeb')

@push('head')
    @vite(['resources/css/dashboard.css'])
@endpush

@section('content')

@php
    $totalPurchases = $purchases->total();
    $activeCount = $purchases->getCollection()->where('status', 'paid')->count();
    $pendingCount = $purchases->getCollection()->where('status', 'pending')->count();
@endphp

{{-- Hero --}}
<div class="dashboard-hero">
    <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-(--color-text) dark:text-white sm:text-3xl">
            Welcome back, {{ auth()->user()->name }}
        </h1>
        <p class="mt-1.5 text-sm text-(--color-text-secondary) sm:text-base">Continue your learning journey.</p>

        <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <div class="stat-tile p-5">
                <div class="text-2xl font-extrabold text-(--color-text) dark:text-white">{{ $totalPurchases }}</div>
                <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Courses</div>
            </div>
            <div class="stat-tile p-5">
                <div class="text-2xl font-extrabold text-(--color-primary)" data-stat-completed-lessons>0</div>
                <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Completed Lessons</div>
            </div>
            <div class="stat-tile p-5">
                <div class="text-2xl font-extrabold text-(--color-success)" data-stat-progress>0%</div>
                <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Progress</div>
            </div>
            <div class="stat-tile p-5">
                <div class="text-2xl font-extrabold text-(--color-accent)" data-stat-hours>0h</div>
                <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Learning Hours</div>
            </div>
        </div>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Continue Learning — populated client-side from localStorage, hidden by default --}}
    <a href="#" id="continueLearningCard" data-continue-learning
       class="lesson-card mb-6 hidden items-center justify-between gap-4 px-5 py-4 transition-colors hover:bg-(--color-primary)/5 sm:flex">
        <div class="flex items-center gap-4 overflow-hidden">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-(--color-primary) text-white">
                <x-icon name="play" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <div class="text-xs font-semibold uppercase tracking-wide text-(--color-primary)">Continue learning</div>
                <div class="truncate text-sm font-semibold" data-continue-learning-title>&nbsp;</div>
            </div>
        </div>
        <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-(--color-primary)" />
    </a>

    {{-- Recently Viewed — populated client-side from localStorage, hidden until there's history --}}
    <div class="mb-8 hidden" data-recently-viewed-section>
        <h2 class="mb-3 text-lg font-bold text-(--color-text) dark:text-white">Recently Viewed</h2>
        <div class="recent-scroll" data-recently-viewed-list></div>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-bold">My Courses</h2>
        <a href="{{ route('courses.index') }}" class="btn-secondary text-sm">
            <x-icon name="layout-grid" class="h-4 w-4" />
            Browse More
        </a>
    </div>

    @if(! $purchases->isEmpty())
        <div class="mb-5 flex flex-wrap items-center gap-2" data-status-filters>
            <button type="button" class="status-pill is-active" data-status-filter="all">All</button>
            <button type="button" class="status-pill" data-status-filter="in-progress">In Progress</button>
            <button type="button" class="status-pill" data-status-filter="completed">Completed</button>
            <button type="button" class="status-pill" data-status-filter="not-started">Not Started</button>
        </div>
    @endif

    @if($purchases->isEmpty())
        <div class="lesson-card flex flex-col items-center px-6 py-20 text-center">
            <span class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="graduation-cap" class="h-8 w-8" />
            </span>
            <h3 class="text-lg font-bold text-(--color-text) dark:text-white">No courses yet</h3>
            <p class="mt-1.5 max-w-sm text-sm text-(--color-text-secondary)">
                You haven't purchased any courses yet. Browse the catalog and start learning today.
            </p>
            <a href="{{ route('courses.index') }}" class="btn-primary mt-6 text-sm">
                <x-icon name="layout-grid" class="h-4 w-4" />
                Browse Courses
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($purchases as $purchase)
                @php
                    $lessons = $purchase->course->lessons()->orderBy('sort_order')->get(['id', 'title']);
                    $lessonsCount = $lessons->count();
                @endphp
                <div
                    class="course-card group flex flex-col overflow-hidden"
                    data-course-card
                    data-status="{{ $purchase->status === 'paid' ? 'not-started' : 'pending' }}"
                    data-course-id="{{ $purchase->course_id }}"
                    data-lessons="@json($lessons->map(fn($l) => ['id' => $l->id, 'title' => $l->title])->values())"
                >
                    <div class="course-card-cover">
                        @if($purchase->course->cover_image_path)
                            <img src="{{ Storage::url($purchase->course->cover_image_path) }}" alt="{{ $purchase->course->title }}" loading="lazy">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <x-icon name="graduation-cap" class="h-10 w-10 text-(--color-primary)/40" />
                            </div>
                        @endif

                        <span class="course-card-badge">{{ $purchase->course->category->name }}</span>

                        @if($purchase->status === 'paid')
                            <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-(--color-success) px-2.5 py-1 text-[11px] font-semibold text-white shadow">
                                <x-icon name="check" class="h-3 w-3" />Active
                            </span>
                            @if($lessonsCount > 0)
                                <svg class="absolute bottom-3 right-3 drop-shadow" viewBox="0 0 36 36" width="40" height="40" data-progress-ring role="img" aria-label="Course progress">
                                    <circle class="progress-ring-track" cx="18" cy="18" r="15.5" fill="none" stroke-width="3"></circle>
                                    <circle class="progress-ring-fill" data-progress-ring-fill cx="18" cy="18" r="15.5" fill="none" stroke-width="3"
                                            stroke-linecap="round" stroke-dasharray="97.39" stroke-dashoffset="97.39" transform="rotate(-90 18 18)"></circle>
                                    <text data-progress-ring-text x="18" y="21" text-anchor="middle" class="progress-ring-label">0%</text>
                                </svg>
                            @endif
                        @else
                            <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-(--color-accent) px-2.5 py-1 text-[11px] font-semibold text-white shadow">
                                <x-icon name="clock" class="h-3 w-3" />Pending
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="text-sm font-bold leading-snug text-(--color-text) dark:text-white">{{ $purchase->course->title }}</h3>
                        <div class="mt-1.5 text-xs text-(--color-text-secondary)">
                            Purchased {{ $purchase->purchased_at->format('d M Y') }}
                        </div>

                        @if($purchase->status === 'paid')
                            <div class="mt-4">
                                <div class="mb-1.5 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-(--color-text) dark:text-white" data-progress-label>
                                        @if($lessonsCount > 0)
                                            0 of {{ $lessonsCount }} {{ Str::plural('lesson', $lessonsCount) }}
                                        @else
                                            No lessons yet
                                        @endif
                                    </span>
                                    <span class="font-bold text-(--color-primary)" data-progress-percent>0%</span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" data-progress-fill style="width: 0%"></div>
                                </div>
                                <p class="mt-2 truncate text-xs text-(--color-text-secondary)" data-continue-label>
                                    @if($lessonsCount > 0)
                                        Continue with: {{ $lessons->first()->title }}
                                    @endif
                                </p>
                            </div>
                        @else
                            <div class="mt-4 text-xs text-(--color-text-secondary)">
                                {{ $lessonsCount }} {{ Str::plural('lesson', $lessonsCount) }}
                            </div>
                        @endif

                        <div class="mt-auto flex flex-col gap-2 pt-4">
                            @if($purchase->status === 'paid')
                                <a href="{{ route('courses.learn', $purchase->course->slug) }}" data-continue-link class="btn-primary w-full text-sm">
                                    <x-icon name="play" class="h-4 w-4" />
                                    <span data-continue-cta>Start Learning</span>
                                </a>
                                <button type="button" disabled title="Certificates are coming soon" data-certificate-badge
                                        class="hidden w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-(--color-border) px-4 py-2 text-xs font-semibold text-(--color-text-secondary) opacity-70 dark:border-white/10">
                                    <x-icon name="shield" class="h-3.5 w-3.5" />
                                    Certificate — Coming Soon
                                </button>
                            @else
                                <div class="flex items-center gap-2 rounded-lg bg-(--color-accent)/10 px-3 py-2 text-xs text-(--color-accent)">
                                    <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" />
                                    Waiting for admin payment confirmation.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $purchases->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    function read(key, fallback) {
        try {
            var raw = localStorage.getItem(key);
            return raw === null ? fallback : JSON.parse(raw);
        } catch (e) { return fallback; }
    }

    // ---- Continue Learning banner ----
    try {
        var raw = localStorage.getItem('continue-learning');
        if (raw) {
            var data = JSON.parse(raw);
            if (data && data.url && data.title) {
                var card = document.getElementById('continueLearningCard');
                card.href = data.url;
                card.querySelector('[data-continue-learning-title]').textContent = data.title;
                card.classList.remove('hidden');
            }
        }
    } catch (e) { /* localStorage unavailable — skip silently */ }

    // ---- Per-course progress (from lp:completed:<courseId>, written by the learn page) ----
    var totalCompleted = 0;
    var totalLessons = 0;

    document.querySelectorAll('[data-course-card]').forEach(function (card) {
        // Pending (unpaid) purchases have no real progress — they're excluded
        // from the status filters entirely rather than miscategorized.
        if (card.dataset.status === 'pending') return;

        var courseId = card.dataset.courseId;
        var lessons = [];
        try {
            lessons = JSON.parse(card.dataset.lessons || '[]');
        } catch (e) {
            lessons = [];
        }
        if (!lessons.length) return;

        var completed = read('lp:completed:' + courseId, []);
        var completedCount = lessons.filter(function (l) { return completed.indexOf(l.id) !== -1; }).length;
        var percent = Math.round((completedCount / lessons.length) * 100);

        totalCompleted += completedCount;
        totalLessons += lessons.length;

        var fill = card.querySelector('[data-progress-fill]');
        var percentEl = card.querySelector('[data-progress-percent]');
        var labelEl = card.querySelector('[data-progress-label]');
        var continueEl = card.querySelector('[data-continue-label]');
        var ctaEl = card.querySelector('[data-continue-cta]');
        var linkEl = card.querySelector('[data-continue-link]');
        var ringFill = card.querySelector('[data-progress-ring-fill]');
        var ringText = card.querySelector('[data-progress-ring-text]');
        var certBadge = card.querySelector('[data-certificate-badge]');

        if (fill) fill.style.width = percent + '%';
        if (percentEl) percentEl.textContent = percent + '%';
        if (labelEl) labelEl.textContent = completedCount + ' of ' + lessons.length + ' lesson' + (lessons.length === 1 ? '' : 's');
        if (ringFill) ringFill.style.strokeDashoffset = String(97.39 * (1 - percent / 100));
        if (ringText) ringText.textContent = percent + '%';

        card.dataset.status = percent >= 100 ? 'completed' : (percent > 0 ? 'in-progress' : 'not-started');

        var nextLesson = lessons.find(function (l) { return completed.indexOf(l.id) === -1; });

        if (percent >= 100) {
            if (continueEl) continueEl.textContent = 'Course completed';
            if (ctaEl) ctaEl.textContent = 'Review Course';
            if (certBadge) { certBadge.classList.remove('hidden'); certBadge.classList.add('flex'); }
        } else if (completedCount > 0) {
            if (continueEl && nextLesson) continueEl.textContent = 'Continue with: ' + nextLesson.title;
            if (ctaEl) ctaEl.textContent = 'Continue Learning';
        }

        if (nextLesson && linkEl) {
            var url = new URL(linkEl.href, window.location.origin);
            url.searchParams.set('lesson', nextLesson.id);
            linkEl.href = url.toString();
        }
    });

    // ---- Status filter pills ----
    var filterButtons = Array.prototype.slice.call(document.querySelectorAll('[data-status-filter]'));
    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');

            var status = btn.dataset.statusFilter;
            document.querySelectorAll('[data-course-card]').forEach(function (card) {
                var matches = status === 'all' || card.dataset.status === status;
                card.classList.toggle('hidden', !matches);
            });
        });
    });

    // ---- Recently Viewed ----
    var recentlyViewed = read('lp:recently-viewed', []);
    if (recentlyViewed.length) {
        var section = document.querySelector('[data-recently-viewed-section]');
        var list = document.querySelector('[data-recently-viewed-list]');
        if (section && list) {
            recentlyViewed.forEach(function (item) {
                var chip = document.createElement('a');
                chip.href = item.url;
                chip.className = 'recent-chip flex items-center gap-3 p-3.5';
                chip.innerHTML =
                    '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5"><polygon points="6 3 20 12 6 21 6 3"/></svg>' +
                    '</span>' +
                    '<span class="min-w-0 flex-1">' +
                        '<span class="block truncate text-xs font-bold text-(--color-text)"></span>' +
                        '<span class="block truncate text-[11px] text-(--color-text-secondary)"></span>' +
                    '</span>';
                var titles = chip.querySelectorAll('span.block');
                titles[0].textContent = item.courseTitle || 'Untitled course';
                titles[1].textContent = item.lessonTitle || '';
                list.appendChild(chip);
            });
            section.classList.remove('hidden');
        }
    }

    // ---- Hero stat tiles ----
    var progressStat = document.querySelector('[data-stat-progress]');
    if (progressStat) {
        progressStat.textContent = totalLessons > 0 ? Math.round((totalCompleted / totalLessons) * 100) + '%' : '0%';
    }
    var completedStat = document.querySelector('[data-stat-completed-lessons]');
    if (completedStat) completedStat.textContent = totalCompleted;

    var hoursStat = document.querySelector('[data-stat-hours]');
    if (hoursStat) {
        var seconds = read('lp:time-spent', 0);
        hoursStat.textContent = (seconds / 3600).toFixed(1) + 'h';
    }
})();
</script>
@endpush
@endsection
