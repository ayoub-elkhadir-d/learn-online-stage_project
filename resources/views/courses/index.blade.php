@extends('layouts.site')

@section('title', 'Courses — ArtiWeb')

@push('head')
    @vite(['resources/css/courses-index.css'])
@endpush

@section('content')

{{-- Hero --}}
<div class="storefront-hero">
    <div class="storefront-hero-grid"></div>
    <div class="relative mx-auto max-w-4xl px-4 py-14 text-center sm:px-6 sm:py-20 lg:px-8">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-(--color-primary)/10 px-3.5 py-1.5 text-xs font-semibold text-(--color-primary)">
            <x-icon name="flame" class="h-3.5 w-3.5" />
            Professional Training Courses
        </span>

        <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-(--color-text) dark:text-white sm:text-5xl">
            Learn new skills,<br class="hidden sm:block">
            <span class="text-(--color-primary)">grow your career</span>
        </h1>

        <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-(--color-text-secondary) sm:text-base">
            Expert-led courses designed to help you master in-demand skills. One-time payment, lifetime access.
        </p>

        {{-- Search — filters the courses currently shown on this page --}}
        <div class="mx-auto mt-8 max-w-xl">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-(--color-text-secondary)" />
                <input
                    type="search"
                    id="courseSearchInput"
                    placeholder="Search courses on this page..."
                    aria-label="Search courses"
                    class="w-full rounded-full border border-(--color-border) bg-(--color-card) py-3.5 pl-12 pr-4 text-sm text-(--color-text) shadow-sm placeholder:text-(--color-text-secondary) focus:border-(--color-primary) focus:outline focus:outline-2 focus:outline-offset-1 focus:outline-(--color-primary)/30 dark:border-white/10 dark:bg-(--color-card-dark) dark:text-white"
                >
            </div>
        </div>

        {{-- Category filters --}}
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
            <a href="{{ route('courses.index') }}" class="filter-pill {{ !request('category') ? 'is-active' : '' }}">
                <x-icon name="layout-grid" class="h-3.5 w-3.5" />
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                   class="filter-pill {{ request('category') === $cat->slug ? 'is-active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Grid --}}
<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

    @if($courses->isEmpty())
        <div class="lesson-card flex flex-col items-center px-6 py-20 text-center">
            <span class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="search" class="h-8 w-8" />
            </span>
            <h3 class="text-lg font-bold text-(--color-text) dark:text-white">No courses found</h3>
            <p class="mt-1.5 max-w-sm text-sm text-(--color-text-secondary)">Try a different category or check back later.</p>
        </div>
    @else
        <div class="mb-6 flex items-center gap-3">
            <h2 class="text-lg font-bold text-(--color-text) dark:text-white">Available Courses</h2>
            <span class="rounded-full bg-(--color-text)/8 px-2.5 py-1 text-xs font-semibold text-(--color-text-secondary) dark:bg-white/10">
                {{ $courses->total() }} {{ Str::plural('course', $courses->total()) }}
            </span>
        </div>

        <div id="courseGrid" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($courses as $course)
                @php
                    $lessonsCount = $course->lessons()->count();
                    $studentsCount = $course->purchases()->where('status', 'paid')->count();
                @endphp
                <div class="course-card group flex flex-col" data-course-item data-course-title="{{ strtolower($course->title) }}">
                    <div class="course-card-cover">
                        @if($course->cover_image_path)
                            <img src="{{ Storage::url($course->cover_image_path) }}" alt="{{ $course->title }}" loading="lazy">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <x-icon name="graduation-cap" class="h-10 w-10 text-(--color-primary)/40" />
                            </div>
                        @endif
                        <span class="course-card-badge">{{ $course->category->name }}</span>
                        <span class="course-card-price">{{ $course->price_mad }} MAD</span>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="text-sm font-bold leading-snug text-(--color-text) dark:text-white">{{ $course->title }}</h3>
                        <p class="mt-2 flex-1 text-xs leading-relaxed text-(--color-text-secondary)">
                            {{ Str::limit($course->description, 90) }}
                        </p>

                        <div class="mt-4 flex items-center gap-4 text-xs text-(--color-text-secondary)">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="book-open" class="h-3.5 w-3.5 text-(--color-primary)" />
                                {{ $lessonsCount }} {{ Str::plural('lesson', $lessonsCount) }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <x-icon name="user" class="h-3.5 w-3.5 text-(--color-primary)" />
                                {{ $studentsCount }} {{ Str::plural('student', $studentsCount) }}
                            </span>
                        </div>

                        <a href="{{ route('courses.show', $course->slug) }}" class="btn-primary mt-5 w-full text-sm">
                            View Course
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="courseSearchEmpty" class="lesson-card mt-5 hidden items-center px-6 py-16 text-center">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="search" class="h-7 w-7" />
            </span>
            <h3 class="text-base font-bold text-(--color-text) dark:text-white">No matches on this page</h3>
            <p class="mt-1 text-sm text-(--color-text-secondary)">Try a different search term, or clear the search to see all courses.</p>
        </div>

        <div class="mt-8">{{ $courses->appends(request()->query())->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    var input = document.getElementById('courseSearchInput');
    if (!input) return;

    var items = Array.prototype.slice.call(document.querySelectorAll('[data-course-item]'));
    var emptyState = document.getElementById('courseSearchEmpty');

    input.addEventListener('input', function () {
        var query = input.value.trim().toLowerCase();
        var visibleCount = 0;

        items.forEach(function (item) {
            var matches = !query || item.dataset.courseTitle.indexOf(query) !== -1;
            item.classList.toggle('hidden', !matches);
            if (matches) visibleCount++;
        });

        var showEmpty = query && visibleCount === 0;
        if (emptyState) {
            emptyState.classList.toggle('hidden', !showEmpty);
            emptyState.classList.toggle('flex', showEmpty);
            emptyState.classList.toggle('flex-col', showEmpty);
        }
    });
})();
</script>
@endpush

@endsection
