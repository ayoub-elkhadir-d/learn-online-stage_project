{{-- Everything in this file is what gets replaced (via innerHTML) when the
     user switches lessons — the player above is never part of this swap.
     Progress/bookmark/note values below render as 0/empty on the server
     (this data is client-only, see resources/js/lesson-progress.js) and are
     repainted instantly on boot by learn-page.js. --}}

<div class="lesson-card p-5 sm:p-6">
    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-(--color-primary)">
        Lesson {{ $lessonNumber }} of {{ $lessons->count() }}
    </p>
    <h1 class="text-xl font-extrabold tracking-tight text-(--color-text) dark:text-white sm:text-2xl">{{ $currentLesson->title }}</h1>
    @if($currentLesson->description)
        <p class="mt-3 text-sm leading-relaxed text-(--color-text-secondary)">{{ $currentLesson->description }}</p>
    @else
        <p class="mt-3 text-sm italic text-(--color-text-secondary)">No description provided for this lesson.</p>
    @endif
</div>

<div class="lesson-card defer-render p-5 sm:p-6">
    <div class="mb-3 flex items-center justify-between gap-3">
        <h2 class="flex items-center gap-1.5 text-sm font-bold text-(--color-text) dark:text-white">
            <x-icon name="trending-up" class="h-4 w-4 text-(--color-text-secondary)" />
            Course Progress
        </h2>
        <span class="text-sm font-bold text-(--color-primary)" data-progress-pct>0%</span>
    </div>
    <div class="h-2 w-full overflow-hidden rounded-full bg-(--color-border)">
        <div data-progress-bar class="h-full rounded-full bg-(--color-primary) transition-all duration-300" style="width:0%"></div>
    </div>
    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-(--color-text-secondary)">
        <span><span data-progress-completed>0</span> of <span data-progress-total>{{ $lessons->count() }}</span> lessons completed</span>
        <span class="flex items-center gap-1">
            <x-icon name="clock" class="h-3.5 w-3.5" />
            <span data-progress-time-spent>0:00</span> spent learning
        </span>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start">
    {{-- Bookmarks: height lives on the card, not the <ul> — the card is a
         fixed-height flex column so the list is the only thing that can
         ever scroll, and growing it can never grow the page. --}}
    <div class="lesson-card defer-render flex h-[300px] min-h-0 flex-col p-5 sm:p-6" data-bookmarks-panel>
        <div class="mb-3 flex shrink-0 items-center justify-between gap-2">
            <h2 class="flex items-center gap-1.5 text-sm font-bold text-(--color-text) dark:text-white">
                <x-icon name="bookmark" class="h-4 w-4 text-(--color-text-secondary)" />
                Bookmarks
            </h2>
            <button type="button" data-bookmark-add class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-(--color-primary)/10 px-2.5 py-1.5 text-xs font-semibold text-(--color-primary) transition-colors hover:bg-(--color-primary)/20">
                <x-icon name="bookmark-plus" class="h-3.5 w-3.5" />
                Add
            </button>
        </div>
        <ul data-bookmarks-list class="bookmarks-scroll flex min-h-0 flex-1 flex-col gap-0.5 overflow-y-auto pr-0.5">
            <li data-bookmarks-empty class="px-1 py-2 text-xs leading-relaxed text-(--color-text-secondary)">
                No bookmarks yet. Click "Add", or press
                <kbd class="rounded border border-(--color-border) px-1 py-0.5 font-sans text-[10px] font-semibold">B</kbd>
                while watching, to save a moment you want to revisit.
            </li>
        </ul>
    </div>

    {{-- Notes --}}
    <div class="lesson-card defer-render p-5 sm:p-6">
        <div class="mb-2 flex items-center justify-between gap-3">
            <h2 class="flex items-center gap-1.5 text-sm font-bold text-(--color-text) dark:text-white">
                <x-icon name="edit" class="h-4 w-4 text-(--color-text-secondary)" />
                My Notes
            </h2>
            <span data-notes-status class="flex items-center gap-1.5 text-xs text-(--color-text-secondary)"></span>
        </div>
        <textarea
            data-notes-field
            rows="6"
            maxlength="2000"
            placeholder="Jot down anything worth remembering from this lesson..."
            class="input-field resize-y text-sm"
        ></textarea>
        <div class="mt-1.5 text-right text-[11px] text-(--color-text-secondary)" data-notes-count>0 / 2000</div>
    </div>
</div>

<div class="flex flex-wrap items-center gap-3 border-t border-(--color-border) pt-6">
    @if($prevLesson)
    <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $prevLesson->id]) }}"
       class="btn-secondary text-sm" data-lesson-nav data-lesson-id="{{ $prevLesson->id }}">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Previous
    </a>
    @endif
    @if($nextLesson)
    <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $nextLesson->id]) }}"
       class="btn-primary ml-auto text-sm" data-lesson-nav data-lesson-id="{{ $nextLesson->id }}" data-next-lesson>
        Next Lesson
        <x-icon name="chevron-right" class="h-4 w-4" />
    </a>
    @else
    <div class="ml-auto flex items-center gap-2 rounded-lg bg-(--color-primary)/10 px-3 py-2 text-sm font-medium text-(--color-primary)">
        <x-icon name="check-circle" class="h-4 w-4" />
        You've reached the last lesson!
    </div>
    @endif
</div>
