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

    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold">My Courses</h2>
        <a href="{{ route('courses.index') }}" class="btn-secondary text-sm">
            <x-icon name="layout-grid" class="h-4 w-4" />
            Browse More
        </a>
    </div>

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

                        <div class="mt-auto pt-4">
                            @if($purchase->status === 'paid')
                                <a href="{{ route('courses.learn', $purchase->course->slug) }}" data-continue-link class="btn-primary w-full text-sm">
                                    <x-icon name="play" class="h-4 w-4" />
                                    <span data-continue-cta>Start Learning</span>
                                </a>
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
        var courseId = card.dataset.courseId;
        var lessons = read('lessons:' + courseId, null);
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

        if (fill) fill.style.width = percent + '%';
        if (percentEl) percentEl.textContent = percent + '%';
        if (labelEl) labelEl.textContent = completedCount + ' of ' + lessons.length + ' lesson' + (lessons.length === 1 ? '' : 's');

        var nextLesson = lessons.find(function (l) { return completed.indexOf(l.id) === -1; });

        if (percent >= 100) {
            if (continueEl) continueEl.textContent = 'Course completed';
            if (ctaEl) ctaEl.textContent = 'Review Course';
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
