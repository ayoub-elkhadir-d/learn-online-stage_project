@php
    $lessonsCount = $course->lessons()->count();
    $studentsCount = $course->purchases()->where('status', 'paid')->count();
    $reviewCount = $course->reviews()->count();
    $avgRating = $reviewCount ? round($course->reviews()->avg('rating'), 1) : null;
    $isEnrolled = auth()->check() && $course->isPurchasedBy(auth()->user());
    $difficultyStyle = [
        'beginner' => 'bg-(--color-success)/10 text-(--color-success)',
        'intermediate' => 'bg-(--color-accent)/15 text-(--color-accent)',
        'advanced' => 'bg-(--color-danger)/10 text-(--color-danger)',
    ][$course->difficulty] ?? 'bg-(--color-text)/8 text-(--color-text-secondary)';
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

    <div class="flex flex-1 flex-col p-4">
        <div class="mb-2 flex items-center gap-2">
            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $difficultyStyle }}">{{ ucfirst($course->difficulty) }}</span>
            @if($avgRating !== null)
                <span class="flex items-center gap-1 text-xs font-semibold text-(--color-text)">
                    <x-icon name="star" class="h-3.5 w-3.5 fill-current text-amber-400" />
                    {{ number_format($avgRating, 1) }}
                    <span class="font-normal text-(--color-text-secondary)">({{ $reviewCount }})</span>
                </span>
            @endif
        </div>

        <h3 class="text-sm font-bold leading-snug text-(--color-text) dark:text-white">{{ $course->title }}</h3>
        <p class="mt-2 flex-1 text-xs leading-relaxed text-(--color-text-secondary)">
            {{ Str::limit($course->description, 90) }}
        </p>

        @if($course->instructor_name)
            <div class="mt-3 flex items-center gap-2">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-[10px] font-bold text-(--color-primary)">
                    {{ strtoupper(substr($course->instructor_name, 0, 1)) }}
                </span>
                <span class="truncate text-xs font-medium text-(--color-text-secondary)">{{ $course->instructor_name }}</span>
            </div>
        @endif

        <div class="mt-3 flex items-center gap-4 text-xs text-(--color-text-secondary)">
            <span class="flex items-center gap-1.5">
                <x-icon name="book-open" class="h-3.5 w-3.5 text-(--color-primary)" />
                {{ $lessonsCount }} {{ Str::plural('lesson', $lessonsCount) }}
            </span>
            <span class="flex items-center gap-1.5">
                <x-icon name="user" class="h-3.5 w-3.5 text-(--color-primary)" />
                {{ $studentsCount }} {{ Str::plural('student', $studentsCount) }}
            </span>
        </div>

        @if($isEnrolled)
            <a href="{{ route('courses.learn', $course->slug) }}" class="btn-primary mt-4 w-full text-sm">
                <x-icon name="play" class="h-4 w-4" />
                Continue Learning
            </a>
        @else
            <a href="{{ route('courses.show', $course->slug) }}" class="btn-primary mt-4 w-full text-sm">
                View Course
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        @endif
    </div>
</div>
