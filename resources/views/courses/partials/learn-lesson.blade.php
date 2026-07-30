@if($currentLesson)
@php
    $ids = $lessons->pluck('id')->toArray();
    $pos = array_search($currentLesson->id, $ids);
    $prevLesson = $pos > 0 ? $lessons[$pos - 1] : null;
    $nextLesson = $pos < count($ids) - 1 ? $lessons[$pos + 1] : null;
@endphp

<div
    data-secure-player
    data-lesson-id="{{ $currentLesson->id }}"
    data-course-id="{{ $course->id }}"
    data-next-url="{{ $nextLesson ? route('courses.learn', [$course->slug, 'lesson' => $nextLesson->id]) : '' }}"
    data-next-id="{{ $nextLesson->id ?? '' }}"
    data-next-title="{{ $nextLesson->title ?? '' }}"
    oncontextmenu="return false;"
    class="relative aspect-video w-full max-h-[65vh] shrink-0 overflow-hidden bg-black sm:rounded-2xl"
>
    <video playsinline poster="{{ $course->cover_image_path ? Storage::url($course->cover_image_path) : '' }}" class="h-full w-full">
        <source src="{{ route('lessons.video', $currentLesson) }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

{{-- Elevated "second section" — clearly separates lesson details from the
     player above via a distinct surface color, rounded top corners, a top
     border, and a soft upward-cast shadow. --}}
<div class="relative rounded-t-2xl border-t border-(--color-border) bg-(--color-card) shadow-[0_-8px_24px_-14px_rgba(0,0,0,0.12)] dark:border-white/10 dark:bg-(--color-card-dark) dark:shadow-[0_-8px_24px_-14px_rgba(0,0,0,0.5)]">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <div class="mb-4 flex items-start justify-between gap-4">
            <h1 class="text-lg font-bold sm:text-xl">{{ $currentLesson->title }}</h1>
        </div>

        @if($currentLesson->description)
            <p class="text-sm leading-relaxed text-(--color-text-secondary)">{{ $currentLesson->description }}</p>
        @else
            <p class="text-sm italic text-(--color-text-secondary)">No description provided for this lesson.</p>
        @endif

        {{-- Notes --}}
        <div class="mt-8">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="flex items-center gap-1.5 text-sm font-bold">
                    <x-icon name="edit" class="h-4 w-4 text-(--color-text-secondary)" />
                    My Notes
                </h2>
                <span data-notes-status class="text-xs text-(--color-text-secondary)"></span>
            </div>
            <textarea
                data-notes-field
                rows="4"
                placeholder="Jot down anything worth remembering from this lesson..."
                class="input-field resize-y text-sm"
            ></textarea>
        </div>

        {{-- Bookmarks --}}
        <div class="mt-6" data-bookmarks-panel>
            <h2 class="mb-2 flex items-center gap-1.5 text-sm font-bold">
                <x-icon name="bookmark" class="h-4 w-4 text-(--color-text-secondary)" />
                Bookmarks
            </h2>
            {{-- Fixed max-height + its own scroll — a long bookmark list must
                 never push the video player around (see shrink-0 above; this
                 is the belt-and-suspenders half of that fix). --}}
            <ul data-bookmarks-list class="flex max-h-52 flex-col gap-1 overflow-y-auto pr-1">
                <li data-bookmarks-empty class="text-xs text-(--color-text-secondary)">
                    Use the bookmark button on the player to save moments you want to revisit.
                </li>
            </ul>
        </div>

        {{-- Prev / Next navigation --}}
        <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-(--color-border) pt-6 dark:border-white/10">
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
</div>
@else
<div class="flex flex-1 flex-col items-center justify-center gap-3 px-6 py-24 text-center text-(--color-text-secondary)">
    <x-icon name="video" class="h-10 w-10 opacity-50" />
    <h2 class="text-sm font-semibold">No lessons available yet</h2>
    <p class="max-w-xs text-xs">The instructor hasn't added any lessons to this course yet. Check back later.</p>
</div>
@endif
