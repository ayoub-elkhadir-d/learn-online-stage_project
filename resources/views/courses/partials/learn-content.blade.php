{{-- Everything in this file is what gets replaced (via innerHTML) when the
     user switches lessons — the player above is never part of this swap.
     Progress/bookmark/review values below render as 0/empty on the server
     (this data is client-only, see resources/js/lesson-progress.js) and are
     repainted instantly on boot by learn-page.js. Overview is always the
     tab shown first — bindTabs() in learn-page.js resets to it on every
     lesson swap since this whole partial is freshly re-rendered anyway. --}}

<div class="lesson-card sticky top-4 z-10 px-4 sm:px-6" data-tabs>
    <div class="tab-nav" role="tablist" aria-label="Lesson sections">
        <span class="tab-indicator" data-tab-indicator></span>

        <button type="button" role="tab" class="tab-btn" id="tab-overview" aria-controls="panel-overview" aria-selected="true" data-tab-btn="overview">
            Overview
        </button>
        <button type="button" role="tab" class="tab-btn" id="tab-bookmarks" aria-controls="panel-bookmarks" aria-selected="false" tabindex="-1" data-tab-btn="bookmarks">
            Bookmarks
        </button>
        <button type="button" role="tab" class="tab-btn" id="tab-reviews" aria-controls="panel-reviews" aria-selected="false" tabindex="-1" data-tab-btn="reviews">
            Ratings &amp; Reviews
        </button>
    </div>
</div>

{{-- Overview --}}
<div id="panel-overview" role="tabpanel" aria-labelledby="tab-overview" class="tab-panel space-y-6" data-tab-panel="overview">
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
</div>

{{-- Bookmarks --}}
<div id="panel-bookmarks" role="tabpanel" aria-labelledby="tab-bookmarks" class="tab-panel" data-tab-panel="bookmarks" hidden>
    {{-- Height lives on the card, not the <ul> — the card is a fixed-height
         flex column so the list is the only thing that can ever scroll, and
         growing it can never grow the page. --}}
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
</div>

{{-- Ratings & Reviews --}}
<div id="panel-reviews" role="tabpanel" aria-labelledby="tab-reviews" class="tab-panel space-y-6" data-tab-panel="reviews" hidden>

    {{-- Summary --}}
    <div class="lesson-card defer-render p-5 sm:p-6">
        <h2 class="mb-4 flex items-center gap-1.5 text-sm font-bold text-(--color-text) dark:text-white">
            <x-icon name="star" class="h-4 w-4 text-(--color-text-secondary)" />
            Ratings &amp; Reviews
        </h2>
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
            <div class="flex shrink-0 flex-col items-center gap-1.5 sm:w-40 sm:border-r sm:border-(--color-border) sm:pr-6">
                <span class="text-4xl font-extrabold leading-none text-(--color-text) dark:text-white" data-review-average>0.0</span>
                <div class="flex items-center gap-0.5" data-review-average-stars></div>
                <span class="text-xs text-(--color-text-secondary)"><span data-review-count>0</span> ratings</span>
            </div>
            <div class="flex-1 space-y-1.5">
                @for ($star = 5; $star >= 1; $star--)
                    <div class="flex items-center gap-2 text-xs">
                        <span class="flex w-6 shrink-0 items-center gap-1 font-medium text-(--color-text-secondary)">{{ $star }}<x-icon name="star" class="h-3 w-3 fill-current" /></span>
                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-(--color-border)">
                            <div class="h-full rounded-full bg-(--color-primary) transition-all duration-300" data-review-bar="{{ $star }}" style="width:0%"></div>
                        </div>
                        <span class="w-9 shrink-0 text-right font-semibold text-(--color-text-secondary)" data-review-bar-pct="{{ $star }}">0%</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Write a review --}}
    <div class="lesson-card defer-render p-5 sm:p-6">
        <h3 class="mb-3 text-sm font-bold text-(--color-text) dark:text-white">Write a Review</h3>
        <div class="flex items-center gap-1" data-review-star-input>
            @for ($star = 1; $star <= 5; $star++)
                <button type="button" data-star-input="{{ $star }}" aria-label="Rate {{ $star }} star{{ $star > 1 ? 's' : '' }}" class="review-star-btn">
                    <x-icon name="star" class="h-7 w-7" />
                </button>
            @endfor
        </div>
        <textarea
            data-review-text
            rows="3"
            maxlength="600"
            placeholder="Share your thoughts about this lesson..."
            class="input-field mt-3 resize-y text-sm"
        ></textarea>
        <div class="mt-3 flex items-center justify-between gap-3">
            <span class="text-xs text-(--color-danger)" data-review-error></span>
            <button type="button" data-review-submit class="btn-primary ml-auto shrink-0 text-sm">
                <x-icon name="check" class="h-4 w-4" />
                Submit Review
            </button>
        </div>
    </div>

    {{-- Review list --}}
    <div data-reviews-list class="space-y-4">
        <div data-reviews-empty class="lesson-card flex flex-col items-center px-6 py-14 text-center">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="star" class="h-7 w-7" />
            </span>
            <h3 class="text-sm font-bold text-(--color-text) dark:text-white">No reviews yet</h3>
            <p class="mt-1 text-xs text-(--color-text-secondary)">Be the first to rate this lesson.</p>
        </div>
    </div>
</div>
