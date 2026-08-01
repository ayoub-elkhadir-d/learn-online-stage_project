@extends('layouts.site')

@section('title', __('courses.hero_badge') . ' — ArtiWeb')

@push('head')
    @vite(['resources/css/courses-index.css'])
@endpush

@section('content')

{{-- Hero — intentionally always-dark (independent of the site's light/dark
     toggle), same technique as modern SaaS landing pages (Linear, Vercel,
     Stripe): a dark hero band on an otherwise light page. Every color below
     is therefore a fixed value, not a --color-* token that would flip with
     the theme. --}}
<div class="storefront-hero">
    <div class="hero-orb hero-orb-1" aria-hidden="true"></div>
    <div class="hero-orb hero-orb-2" aria-hidden="true"></div>
    <div class="storefront-hero-grid" aria-hidden="true"></div>
    <div class="hero-shape hero-shape-1" aria-hidden="true"></div>
    <div class="hero-shape hero-shape-2" aria-hidden="true"></div>
    <div class="hero-shape hero-shape-3" aria-hidden="true"></div>
    <div class="hero-line hero-line-1" aria-hidden="true"></div>
    <div class="hero-line hero-line-2" aria-hidden="true"></div>
    <div class="hero-dot hero-dot-1" aria-hidden="true"></div>
    <div class="hero-dot hero-dot-2" aria-hidden="true"></div>
    <div class="hero-dot hero-dot-3" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-4xl px-4 py-6 text-center sm:px-6 sm:py-8 lg:px-8">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-(--color-primary)/15 px-3.5 py-1.5 text-xs font-semibold text-(--color-accent)">
            <x-icon name="flame" class="h-3.5 w-3.5" />
            {{ __('courses.hero_badge') }}
        </span>

        <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">
            {{ __('courses.hero_title_start') }}<br class="hidden sm:block">
            <span class="brand-shimmer">{{ __('courses.hero_title_brand') }}</span> {{ __('courses.hero_title_end') }}
        </h1>

        <p class="mx-auto mt-2 max-w-xl text-sm leading-relaxed text-[#CBD5E1] sm:text-base">
            {{ __('courses.hero_subtitle') }}
        </p>

        {{-- Search — filters the courses currently shown on this page --}}
        <div class="mx-auto mt-4 max-w-xl">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#94A3B8]" />
                <input
                    type="search"
                    id="courseSearchInput"
                    placeholder="{{ __('courses.search_placeholder') }}"
                    aria-label="{{ __('courses.search_placeholder') }}"
                    class="w-full rounded-full border border-white/10 bg-white/5 py-3 pl-12 pr-4 text-sm text-white placeholder:text-[#94A3B8] backdrop-blur-sm focus:border-(--color-primary) focus:outline focus:outline-2 focus:outline-offset-1 focus:outline-(--color-primary)/30"
                >
            </div>
        </div>

        {{-- Category filters --}}
        <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
            <a href="{{ route('courses.index') }}" class="filter-pill {{ !request('category') ? 'is-active' : '' }}">
                <x-icon name="layout-grid" class="h-3.5 w-3.5" />
                {{ __('courses.category_all') }}
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

{{-- Body — a single category filter (from a pill / "View All") shows the
     classic paginated grid for that one category; the unfiltered default
     view groups courses by category into Udemy-style horizontal rows. --}}
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

    @if($courses->isEmpty())
        <div class="lesson-card flex flex-col items-center px-6 py-20 text-center">
            <span class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="search" class="h-8 w-8" />
            </span>
            <h3 class="text-lg font-bold text-(--color-text) dark:text-white">{{ __('courses.no_courses_found') }}</h3>
            <p class="mt-1.5 max-w-sm text-sm text-(--color-text-secondary)">{{ __('courses.no_courses_hint') }}</p>
        </div>

    @elseif(request('category'))

        <div class="mb-6 flex items-center gap-3" data-animate="fade-up">
            <h2 class="text-lg font-bold text-(--color-text) dark:text-white">{{ __('courses.available_courses') }}</h2>
            <span class="rounded-full bg-(--color-text)/8 px-2.5 py-1 text-xs font-semibold text-(--color-text-secondary) dark:bg-white/10">
                {{ trans_choice('courses.courses_count', $courses->total(), ['count' => $courses->total()]) }}
            </span>
        </div>

        <div id="courseGrid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($courses as $index => $course)
                <div data-animate="fade-up" style="transition-delay: {{ min($index, 8) * 60 }}ms">
                    @include('courses.partials.course-card', ['course' => $course])
                </div>
            @endforeach
        </div>

        <div id="courseSearchEmpty" class="lesson-card mt-5 hidden items-center px-6 py-16 text-center">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="search" class="h-7 w-7" />
            </span>
            <h3 class="text-base font-bold text-(--color-text) dark:text-white">{{ __('courses.no_matches') }}</h3>
            <p class="mt-1 text-sm text-(--color-text-secondary)">{{ __('courses.no_matches_hint') }}</p>
        </div>

        <div class="mt-8">{{ $courses->appends(request()->query())->links() }}</div>

    @else

        <div class="flex flex-col gap-12">
            @foreach($categories as $category)
                @php
                    $categoryCourses = $category->courses()->with('category')->latest()->take(10)->get();
                    $categoryTotal = $category->courses()->count();
                @endphp
                @continue($categoryCourses->isEmpty())

                <section data-animate="fade-up">
                    <div class="mb-4 flex items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-(--color-text) dark:text-white sm:text-xl">{{ $category->name }}</h2>
                            <p class="mt-0.5 text-xs text-(--color-text-secondary)">{{ trans_choice('courses.courses_count', $categoryTotal, ['count' => $categoryTotal]) }}</p>
                        </div>
                        <a href="{{ route('courses.index', ['category' => $category->slug]) }}"
                           class="flex shrink-0 items-center gap-1 text-sm font-semibold text-(--color-primary) transition-colors hover:text-(--color-primary-dark)">
                            {{ __('courses.view_all') }}
                            <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>

                    <div class="-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth px-4 pb-2 scrollbar-thin sm:mx-0 sm:px-0">
                        @foreach($categoryCourses as $index => $course)
                            <div class="w-64 shrink-0 snap-start sm:w-72" data-animate="fade-up" style="transition-delay: {{ min($index, 6) * 60 }}ms">
                                @include('courses.partials.course-card', ['course' => $course])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <div id="courseSearchEmpty" class="lesson-card mt-5 hidden items-center px-6 py-16 text-center">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="search" class="h-7 w-7" />
            </span>
            <h3 class="text-base font-bold text-(--color-text) dark:text-white">{{ __('courses.no_matches_categories') }}</h3>
            <p class="mt-1 text-sm text-(--color-text-secondary)">{{ __('courses.no_matches_categories_hint') }}</p>
        </div>
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
            // Hide the closest animated wrapper (the actual grid/row item),
            // not just the card itself — otherwise a filtered-out card still
            // reserves its slot in the grid/horizontal row.
            var target = item.closest('[data-animate]') || item;
            var matches = !query || item.dataset.courseTitle.indexOf(query) !== -1;
            target.classList.toggle('hidden', !matches);
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
