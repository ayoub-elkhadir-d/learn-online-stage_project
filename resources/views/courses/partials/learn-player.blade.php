@php
    $videoSrc = route('lessons.video', $currentLesson);
    $posterUrl = $course->cover_image_path ? Storage::url($course->cover_image_path) : '';
@endphp

{{-- This entire section is rendered once and then left alone by JS — lesson
     navigation only reassigns the <media-player> source (see player.js /
     learn-page.js), it never rebuilds this markup. That's what lets the
     player instance, its volume/speed/fullscreen state, and its internal
     Vidstack UI survive switching lessons untouched. --}}
<section id="playerRegion" class="relative">
    <div class="player-shell relative aspect-video w-full">
        <media-player
            class="h-full w-full"
            title="{{ $currentLesson->title }}"
            data-lesson-id="{{ $currentLesson->id }}"
            data-course-id="{{ $course->id }}"
            data-src="{{ $videoSrc }}"
            data-next-url="{{ $nextLesson ? route('courses.learn', [$course->slug, 'lesson' => $nextLesson->id]) : '' }}"
            data-next-id="{{ $nextLesson->id ?? '' }}"
            data-next-title="{{ $nextLesson->title ?? '' }}"
            preload="metadata"
            playsinline
        >
            <media-provider>
                @if($posterUrl)
                <media-poster class="vds-poster" src="{{ $posterUrl }}" alt="{{ $course->title }}"></media-poster>
                @endif
                <source src="{{ $videoSrc }}" type="video/mp4">
            </media-provider>
            <media-video-layout></media-video-layout>
        </media-player>

        {{-- Auto-advance overlay: app-level UI beside the player, not part of
             Vidstack's own control surface. Hidden until playback ends. --}}
        <div data-up-next class="up-next-panel pointer-events-none absolute inset-0 z-40 hidden flex-col items-center justify-center gap-3 rounded-2xl bg-black/90 px-6 text-center backdrop-blur-sm">
            <div class="pointer-events-auto flex flex-col items-center gap-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Up next</p>
                <p data-up-next-title class="max-w-md text-lg font-bold text-white"></p>
                <p class="text-sm text-white/60">Starting in <span data-up-next-count>5</span>s</p>
                <div class="flex flex-wrap items-center justify-center gap-2.5">
                    <button type="button" data-up-next-replay class="btn-secondary !border-white/20 !bg-white/10 !text-white text-sm">
                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                        Replay
                    </button>
                    <a href="#" data-up-next-link data-lesson-nav class="btn-primary text-sm">Play now</a>
                    <button type="button" data-up-next-cancel class="text-xs font-medium text-white/50 underline hover:text-white/80">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</section>
