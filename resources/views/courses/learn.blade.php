<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentLesson ? $currentLesson->title . ' — ' : '' }}{{ $course->title }} — Learn</title>
    @include('partials.theme-init')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if($course->cover_image_path)
    <link rel="preload" as="image" href="{{ Storage::url($course->cover_image_path) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/css/learn.css', 'resources/js/app.js', 'resources/js/learn.js'])
    <script>
        window.__courseId = {{ $course->id }};
        window.__totalLessons = {{ $lessons->count() }};
    </script>
</head>
<body class="learn-shell h-screen overflow-hidden bg-(--color-bg-light) font-sans text-(--color-text) antialiased dark:bg-(--color-bg-dark) dark:text-[#ECECEC]">

<div class="flex h-full flex-col">

    {{-- Topbar --}}
    <header class="flex shrink-0 items-center gap-3 border-b border-(--color-border) bg-(--color-card) px-4 py-3 dark:bg-(--color-card-dark)">
        <button type="button" data-sidebar-toggle aria-label="Toggle lesson list"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 lg:hidden dark:hover:bg-white/10">
            <x-icon name="list" class="h-5 w-5" />
        </button>

        <a href="{{ route('courses.show', $course->slug) }}" class="flex min-w-0 items-center gap-2 text-sm text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
            <x-icon name="chevron-left" class="h-4 w-4 shrink-0" />
            <span class="truncate font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</span>
        </a>

        <div class="ml-auto flex items-center gap-3">
            <div class="hidden items-center gap-2 sm:flex">
                <div class="h-1.5 w-32 overflow-hidden rounded-full bg-(--color-border)">
                    <div data-progress-bar class="h-full rounded-full bg-(--color-primary) transition-all duration-300" style="width:0%"></div>
                </div>
                <span data-progress-pct class="text-xs font-semibold tabular-nums text-(--color-text-secondary)">0%</span>
            </div>

            <button type="button" data-theme-toggle aria-label="Toggle dark mode"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 dark:hover:bg-white/10">
                <x-icon name="sun" class="hidden h-4.5 w-4.5 dark:block" />
                <x-icon name="moon" class="block h-4.5 w-4.5 dark:hidden" />
            </button>

            <a href="{{ route('dashboard') }}" class="hidden items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) sm:inline-flex dark:hover:text-white">
                <x-icon name="layout-grid" class="h-4 w-4" />
                My Courses
            </a>
        </div>
    </header>

    {{-- min-h-0 is load-bearing here: without it, a flex item's automatic
         min-height is its content size (not 0), so a tall sidebar/content
         column would force this row — and the body under it — to grow past
         the viewport instead of handing overflow to the scrollers below. --}}
    <div class="flex min-h-0 flex-1 overflow-hidden">

        {{-- Sidebar --}}
        <aside data-sidebar class="scrollbar-thin fixed inset-y-0 left-0 z-40 mt-[57px] w-[86%] max-w-[340px] -translate-x-full overflow-y-auto border-r border-(--color-border) bg-(--color-card) transition-transform duration-200 lg:static lg:mt-0 lg:min-h-0 lg:w-[340px] lg:max-w-none lg:translate-x-0 lg:shrink-0 dark:bg-(--color-card-dark)">
            <div class="sticky top-0 z-10 flex items-center gap-2 border-b border-(--color-border) bg-(--color-card) p-3 dark:bg-(--color-card-dark)">
                <div class="relative flex-1">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                    <input type="search" data-lesson-search placeholder="Search lessons..." aria-label="Search lessons"
                           class="input-field !py-2 pl-9 text-sm">
                </div>
                <button type="button" data-sidebar-close aria-label="Close lesson list"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 lg:hidden dark:hover:bg-white/10">
                    <x-icon name="x" class="h-4.5 w-4.5" />
                </button>
            </div>

            <div class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-(--color-text-secondary)">
                {{ $lessons->count() }} {{ Str::plural('lesson', $lessons->count()) }}
            </div>

            <nav id="lessonSidebar" class="flex flex-col gap-0.5 px-2 pb-4">
                @foreach($lessons as $index => $lesson)
                <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $lesson->id]) }}"
                   class="lesson-row group flex items-start gap-3 rounded-xl border-l-2 border-transparent px-3 py-3 text-left"
                   data-lesson-nav
                   data-lesson-id="{{ $lesson->id }}"
                   data-lesson-name="{{ strtolower($lesson->title) }}"
                   data-active="{{ $currentLesson && $currentLesson->id === $lesson->id ? 'true' : 'false' }}">
                    <span class="lesson-badge relative mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-bold transition-colors">
                        <span data-lesson-number>{{ $index + 1 }}</span>
                        <span data-lesson-check class="absolute inset-0 hidden items-center justify-center rounded-full bg-(--color-primary) text-white">
                            <x-icon name="check" class="h-3.5 w-3.5" />
                        </span>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="lesson-title block truncate text-sm font-semibold text-(--color-text) dark:text-white/90">{{ $lesson->title }}</span>
                        <span class="mt-1 flex items-center gap-1.5 text-xs text-(--color-text-secondary)">
                            <x-icon name="clock" class="h-3 w-3 shrink-0" />
                            <span data-lesson-duration="{{ $lesson->id }}" class="skeleton inline-block h-3 w-9 rounded tabular-nums">&nbsp;</span>
                        </span>
                    </span>
                </a>
                @endforeach
            </nav>
        </aside>

        <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>

        {{-- Player + lesson content — min-h-0 lets this box stop at the
             row's height and hand its own overflow to its scrollbar instead
             of stretching the row above it to fit however much content
             (notes, bookmarks, long descriptions) it holds. --}}
        <main class="scrollbar-thin flex min-h-0 flex-1 flex-col overflow-y-auto">
            @if($currentLesson)
                @php
                    $ids = $lessons->pluck('id')->toArray();
                    $pos = array_search($currentLesson->id, $ids);
                    $prevLesson = $pos > 0 ? $lessons[$pos - 1] : null;
                    $nextLesson = $pos < count($ids) - 1 ? $lessons[$pos + 1] : null;
                    $lessonNumber = $pos !== false ? $pos + 1 : 1;
                @endphp
                <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl">
                    @include('courses.partials.learn-player', [
                        'course' => $course,
                        'currentLesson' => $currentLesson,
                        'nextLesson' => $nextLesson,
                    ])
                    <div class="mx-auto w-full max-w-4xl">
                        <div id="lessonContent" class="mt-6 space-y-6">
                            @include('courses.partials.learn-content', [
                                'course' => $course,
                                'lessons' => $lessons,
                                'currentLesson' => $currentLesson,
                                'prevLesson' => $prevLesson,
                                'nextLesson' => $nextLesson,
                                'lessonNumber' => $lessonNumber,
                            ])
                        </div>
                    </div>
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center gap-3 px-6 py-24 text-center text-(--color-text-secondary)">
                    <x-icon name="video" class="h-10 w-10 opacity-50" />
                    <h2 class="text-sm font-semibold text-(--color-text) dark:text-white">No lessons available yet</h2>
                    <p class="max-w-xs text-xs">The instructor hasn't added any lessons to this course yet. Check back later.</p>
                </div>
            @endif
        </main>
    </div>
</div>
</body>
</html>
