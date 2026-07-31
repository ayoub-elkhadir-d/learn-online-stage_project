@extends('layouts.site')

@section('title', $course->title . ' — ArtiWeb')

@push('head')
    @vite(['resources/css/course-details.css'])
@endpush

@section('content')

@php
    $isPaid = $purchase && $purchase->status === 'paid';
    $isPending = $purchase && $purchase->status === 'pending';
    $lessons = $course->lessons()->orderBy('sort_order')->get();
    $reviewCount = $course->reviews()->count();
    $avgRating = $reviewCount ? round($course->reviews()->avg('rating'), 1) : null;
    $recentReviews = $reviewCount ? $course->reviews()->with('user')->latest()->take(3)->get() : collect();
@endphp

<div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Back to courses
    </a>
</div>

{{-- Hero --}}
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.6fr_1fr] lg:items-start">

        <div>
            <div class="course-hero-cover relative aspect-video w-full">
                @if($course->cover_image_path)
                    <img src="{{ Storage::url($course->cover_image_path) }}" alt="{{ $course->title }}" class="h-full w-full" decoding="async" fetchpriority="high">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-(--color-primary)/10">
                        <x-icon name="graduation-cap" class="h-16 w-16 text-(--color-primary)/40" />
                    </div>
                @endif
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-(--color-primary)/10 px-3 py-1 text-xs font-semibold text-(--color-primary)">
                    <x-icon name="layers" class="h-3.5 w-3.5" />
                    {{ $course->category->name }}
                </span>
                @if($avgRating !== null)
                    <span class="inline-flex items-center gap-1 rounded-full bg-(--color-text)/8 px-3 py-1 text-xs font-semibold text-(--color-text) dark:bg-white/10 dark:text-white">
                        <x-icon name="star" class="h-3.5 w-3.5 fill-current text-amber-400" />
                        {{ number_format($avgRating, 1) }}
                        <span class="font-normal text-(--color-text-secondary)">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                    </span>
                @endif
            </div>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-(--color-text) dark:text-white sm:text-3xl">{{ $course->title }}</h1>

            @if($course->description)
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-(--color-text-secondary) sm:text-base">
                    {{ Str::limit($course->description, 180) }}
                </p>
            @endif

            <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-(--color-text-secondary)">
                <span class="flex items-center gap-1.5">
                    <x-icon name="book-open" class="h-4 w-4 text-(--color-primary)" />
                    {{ $lessons->count() }} {{ Str::plural('lesson', $lessons->count()) }}
                </span>
                <span class="flex items-center gap-1.5">
                    <x-icon name="calendar" class="h-4 w-4 text-(--color-primary)" />
                    Updated {{ $course->updated_at->diffForHumans() }}
                </span>
            </div>

            @if($course->instructor_name)
                <div class="mt-6 flex items-center gap-3 rounded-xl border border-(--color-border) p-4 dark:border-white/10">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-sm font-bold text-(--color-primary)">
                        {{ strtoupper(substr($course->instructor_name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">Instructor</div>
                        <div class="truncate text-sm font-bold text-(--color-text) dark:text-white">{{ $course->instructor_name }}</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Enrollment card --}}
        <div class="lesson-card p-6 lg:sticky lg:top-24">
            <div class="text-center">
                <div class="text-3xl font-extrabold text-(--color-text) dark:text-white">
                    {{ $course->price_mad }} <span class="text-base font-medium text-(--color-text-secondary)">MAD</span>
                </div>
                <p class="mt-1 text-xs text-(--color-text-secondary)">One-time payment — lifetime access</p>
            </div>

            <div class="mt-5">
                @auth
                    @if($isPaid)
                        <div class="mb-4 flex flex-col items-center gap-2 text-center">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-(--color-success)/10 text-(--color-success)">
                                <x-icon name="check" class="h-5 w-5" />
                            </span>
                            <h3 class="text-sm font-bold text-(--color-text) dark:text-white">You have access!</h3>
                            <p class="text-xs text-(--color-text-secondary)">Purchased on {{ $purchase->purchased_at->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('courses.learn', $course->slug) }}" class="btn-primary w-full text-sm">
                            <x-icon name="play" class="h-4 w-4" />
                            Start Learning
                        </a>
                    @elseif($isPending)
                        <div class="mb-4 flex flex-col items-center gap-2 text-center">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-(--color-accent)/10 text-(--color-accent)">
                                <x-icon name="clock" class="h-5 w-5" />
                            </span>
                            <h3 class="text-sm font-bold text-(--color-text) dark:text-white">Payment Pending</h3>
                            <p class="text-xs text-(--color-text-secondary)">Awaiting admin confirmation.</p>
                        </div>
                        <div class="rounded-lg bg-(--color-accent)/10 px-3 py-2.5 text-center text-xs text-(--color-accent)">
                            Reference: <strong>{{ $purchase->reference ?? 'N/A' }}</strong>
                        </div>
                    @else
                        <a href="{{ route('courses.checkout', $course->slug) }}" class="btn-primary w-full text-sm">
                            <x-icon name="credit-card" class="h-4 w-4" />
                            Proceed to Checkout
                        </a>
                        <p class="mt-3 flex items-center justify-center gap-1 text-center text-[11px] text-(--color-text-secondary)">
                            <x-icon name="shield" class="h-3.5 w-3.5 text-(--color-primary)" />
                            Secure payment — Admin confirmation required
                        </p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-primary w-full text-sm">
                        Login to Purchase
                    </a>
                @endauth
            </div>

            <ul class="mt-6 flex flex-col text-sm">
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    Full course access
                </li>
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    Certificate upon completion
                </li>
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    Payment confirmed by admin
                </li>
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    Lifetime access
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- About + Course Content — same narrow reading column as the Learning
     page's lesson content, so the two pages feel like one continuous flow. --}}
<div class="mx-auto max-w-4xl px-4 pb-16 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-6">
        <div class="lesson-card p-5 sm:p-6">
            <h2 class="text-lg font-bold text-(--color-text) dark:text-white">About this course</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-(--color-text-secondary)">{{ $course->description ?: 'No description available.' }}</p>
        </div>

        <div class="lesson-card overflow-hidden">
            <div class="flex items-center justify-between gap-3 p-5 pb-4 sm:p-6 sm:pb-4">
                <h2 class="flex items-center gap-2 text-lg font-bold text-(--color-text) dark:text-white">
                    <x-icon name="video" class="h-5 w-5 text-(--color-primary)" />
                    Course Content
                </h2>
                <span class="shrink-0 rounded-full bg-(--color-text)/8 px-2.5 py-1 text-xs font-semibold text-(--color-text-secondary) dark:bg-white/10">
                    {{ $lessons->count() }} {{ Str::plural('lesson', $lessons->count()) }}
                </span>
            </div>

            @if($lessons->isEmpty())
                <p class="px-5 pb-6 text-sm text-(--color-text-secondary) sm:px-6">No lessons have been added to this course yet.</p>
            @else
                <div>
                    @foreach($lessons as $index => $lesson)
                        <details class="lesson-row-details group">
                            <summary class="flex items-center gap-3 px-5 py-4 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-(--color-primary) sm:px-6">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-text)/8 text-xs font-bold text-(--color-text-secondary) dark:bg-white/10">
                                    {{ $index + 1 }}
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-(--color-text) dark:text-white/90">{{ $lesson->title }}</span>
                                    <span class="mt-0.5 flex items-center gap-1.5 text-xs text-(--color-text-secondary)">
                                        <x-icon name="clock" class="h-3 w-3" />
                                        <span data-lesson-duration="{{ $lesson->id }}">—</span>
                                    </span>
                                </span>
                                @if($isPaid)
                                    <span data-lesson-check="{{ $lesson->id }}" class="hidden shrink-0 items-center text-(--color-success)">
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                    </span>
                                @else
                                    <span class="shrink-0 text-(--color-text-secondary)" title="Purchase this course to unlock">
                                        <x-icon name="lock" class="h-4 w-4" />
                                    </span>
                                @endif
                                <x-icon name="chevron-down" data-chevron class="h-4 w-4 shrink-0 text-(--color-text-secondary)" />
                            </summary>
                            <div class="px-5 pb-5 pl-[3.75rem] text-sm leading-relaxed text-(--color-text-secondary) sm:px-6 sm:pl-[4.25rem]">
                                {{ $lesson->description ?: 'No description provided for this lesson.' }}
                                @if($isPaid)
                                    <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $lesson->id]) }}" class="mt-3 flex w-fit items-center gap-1.5 text-sm font-semibold text-(--color-primary) transition-colors hover:text-(--color-primary-dark)">
                                        <x-icon name="play" class="h-3.5 w-3.5" />
                                        Watch lesson
                                    </a>
                                @endif
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lesson-card p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="flex items-center gap-2 text-lg font-bold text-(--color-text) dark:text-white">
                    <x-icon name="star" class="h-5 w-5 text-(--color-primary)" />
                    Ratings &amp; Reviews
                </h2>
                @if($avgRating !== null)
                    <span class="flex shrink-0 items-center gap-1 text-sm font-semibold text-(--color-text) dark:text-white">
                        <x-icon name="star" class="h-4 w-4 fill-current text-amber-400" />
                        {{ number_format($avgRating, 1) }}
                        <span class="font-normal text-(--color-text-secondary)">({{ $reviewCount }})</span>
                    </span>
                @endif
            </div>

            @if($recentReviews->isEmpty())
                <p class="mt-3 text-sm text-(--color-text-secondary)">No reviews yet — be the first to review this course after enrolling.</p>
            @else
                <div class="mt-4 flex flex-col gap-3">
                    @foreach($recentReviews as $review)
                        @include('courses.partials.reviews-review-card', ['review' => $review])
                    @endforeach
                </div>
                @if($reviewCount > $recentReviews->count())
                    <p class="mt-3 text-xs text-(--color-text-secondary)">See all {{ $reviewCount }} reviews from the Learning page after enrolling.</p>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    try {
        var courseId = {{ $course->id }};
        var completed = JSON.parse(localStorage.getItem('lp:completed:' + courseId) || '[]');
        completed.forEach(function (lessonId) {
            var el = document.querySelector('[data-lesson-check="' + lessonId + '"]');
            if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
        });
        document.querySelectorAll('[data-lesson-duration]').forEach(function (el) {
            var raw = localStorage.getItem('lp:duration:' + el.dataset.lessonDuration);
            if (!raw) return;
            var seconds = parseInt(raw, 10);
            if (!seconds) return;
            var m = Math.floor(seconds / 60), s = seconds % 60;
            el.textContent = m + ':' + String(s).padStart(2, '0');
        });
    } catch (e) { /* localStorage unavailable — skip silently */ }
})();
</script>
@endpush

@endsection
