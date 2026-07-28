@if($currentLesson)
<div class="video-wrapper">
    <div class="video-loading-overlay">
        <div class="spinner-ring"></div>
    </div>
    <video controls autoplay controlsList="nodownload" oncontextmenu="return false;">
        <source src="{{ route('lessons.video', $currentLesson) }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
<div class="lesson-detail">
    <h4>{{ $currentLesson->title }}</h4>
    @if($currentLesson->description)
    <p>{{ $currentLesson->description }}</p>
    @else
    <p class="fst-italic">No description provided for this lesson.</p>
    @endif

    {{-- Prev / Next navigation --}}
    @php
        $ids = $lessons->pluck('id')->toArray();
        $pos = array_search($currentLesson->id, $ids);
        $prevLesson = $pos > 0 ? $lessons[$pos - 1] : null;
        $nextLesson = $pos < count($ids) - 1 ? $lessons[$pos + 1] : null;
    @endphp
    <div class="d-flex gap-3 mt-4">
        @if($prevLesson)
        <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $prevLesson->id]) }}"
           class="btn btn-outline-secondary btn-sm" data-lesson-nav data-lesson-id="{{ $prevLesson->id }}">
            <i class="fas fa-chevron-left me-1"></i>Previous
        </a>
        @endif
        @if($nextLesson)
        <a href="{{ route('courses.learn', [$course->slug, 'lesson' => $nextLesson->id]) }}"
           class="btn btn-sm text-white" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;border-radius:8px;"
           data-lesson-nav data-lesson-id="{{ $nextLesson->id }}" data-next-lesson>
            Next <i class="fas fa-chevron-right ms-1"></i>
        </a>
        @else
        <div class="rounded-3 px-3 py-2 d-flex align-items-center gap-2"
             style="background:rgba(16,185,129,.15);color:#6ee7b7;font-size:13px;">
            <i class="fas fa-check-circle"></i> You've reached the last lesson!
        </div>
        @endif
    </div>
</div>
@else
<div class="no-lessons">
    <i class="fas fa-video fa-3x mb-3"></i>
    <h6>No lessons available yet</h6>
    <p style="font-size:13px;">The instructor hasn't added any lessons to this course yet. Check back later.</p>
</div>
@endif
