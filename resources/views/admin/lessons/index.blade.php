@extends('admin.layout')
@section('title', 'Lessons — ' . $course->title)

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
            <x-icon name="chevron-left" class="h-4 w-4" />
            Back to Courses
        </a>
        <h2 class="mt-1.5 text-lg font-bold text-(--color-text) dark:text-white">{{ $course->title }}</h2>
        <p class="text-sm text-(--color-text-secondary)">{{ $lessons->count() }} {{ Str::plural('lesson', $lessons->count()) }}</p>
    </div>
    <a href="{{ route('admin.courses.lessons.create', $course) }}" class="btn-primary">
        <x-icon name="video" class="h-4 w-4" />
        Add Lesson
    </a>
</div>

@if($lessons->isEmpty())
    <div class="lesson-card flex flex-col items-center px-6 py-16 text-center">
        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
            <x-icon name="video" class="h-7 w-7" />
        </span>
        <h3 class="text-base font-bold text-(--color-text) dark:text-white">No lessons yet</h3>
        <p class="mt-1 max-w-sm text-sm text-(--color-text-secondary)">Add your first lesson to start building this course's curriculum.</p>
        <a href="{{ route('admin.courses.lessons.create', $course) }}" class="btn-primary mt-5">
            <x-icon name="video" class="h-4 w-4" />
            Add Lesson
        </a>
    </div>
@else

    {{-- Desktop / tablet table --}}
    <div class="lesson-card hidden overflow-x-auto md:block">
        <table class="admin-table w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Order</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Title</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Description</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Video</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lessons as $lesson)
                    <tr class="border-t border-(--color-border) transition-colors hover:bg-black/[.02] dark:border-white/10 dark:hover:bg-white/[.03]">
                        <td class="px-4 py-3">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">{{ $lesson->sort_order }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-(--color-text) dark:text-white">{{ $lesson->title }}</td>
                        <td class="max-w-xs px-4 py-3 text-xs text-(--color-text-secondary)">{{ Str::limit($lesson->description, 60) ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex max-w-[12rem] items-center gap-1 truncate rounded-full bg-(--color-success)/10 px-2.5 py-1 text-xs font-medium text-(--color-success)" title="{{ basename($lesson->video_path) }}">
                                <x-icon name="video" class="h-3 w-3 shrink-0" />
                                <span class="truncate">{{ Str::limit(basename($lesson->video_path), 28) }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" title="Edit"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}"
                                      onsubmit="return confirm('Delete \'{{ $lesson->title }}\'? The video file will also be removed.')">
                                    @csrf @method('DELETE')
                                    <button title="Delete" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                        <x-icon name="x" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile card list --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($lessons as $lesson)
            <div class="lesson-card p-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">{{ $lesson->sort_order }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $lesson->title }}</div>
                        <div class="mt-0.5 truncate text-xs text-(--color-text-secondary)">{{ Str::limit($lesson->description, 60) ?: '—' }}</div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" class="btn-secondary flex-1 !py-1.5 text-xs">
                        <x-icon name="edit" class="h-3.5 w-3.5" />
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" class="flex-1"
                          onsubmit="return confirm('Delete \'{{ $lesson->title }}\'? The video file will also be removed.')">
                        @csrf @method('DELETE')
                        <button class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                            <x-icon name="x" class="h-3.5 w-3.5" />
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
