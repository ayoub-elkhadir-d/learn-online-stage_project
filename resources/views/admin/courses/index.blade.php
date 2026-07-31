@extends('admin.layout')
@section('title', 'Courses')

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-(--color-text) dark:text-white">All Courses</h2>
        <p class="text-sm text-(--color-text-secondary)">{{ $courses->total() }} {{ Str::plural('course', $courses->total()) }} total</p>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn-primary">
        <x-icon name="book-open" class="h-4 w-4" />
        Add Course
    </a>
</div>

<form method="GET" action="{{ route('admin.courses.index') }}" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
    <div class="relative max-w-sm flex-1">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search courses..." class="input-field pl-9">
    </div>
    <div class="relative sm:w-56">
        <x-icon name="sliders" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
        <select name="category" onchange="this.form.submit()" class="input-field appearance-none pl-9">
            <option value="">All categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <noscript><button class="btn-secondary">Filter</button></noscript>
</form>

@if($courses->isEmpty())
    <div class="lesson-card flex flex-col items-center px-6 py-16 text-center">
        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
            <x-icon name="book-open" class="h-7 w-7" />
        </span>
        <h3 class="text-base font-bold text-(--color-text) dark:text-white">No courses found</h3>
        <p class="mt-1 max-w-sm text-sm text-(--color-text-secondary)">
            @if(request('q') || request('category')) Try a different search or filter. @else Create your first course to get started. @endif
        </p>
        @unless(request('q') || request('category'))
            <a href="{{ route('admin.courses.create') }}" class="btn-primary mt-5">
                <x-icon name="book-open" class="h-4 w-4" />
                Add Course
            </a>
        @endunless
    </div>
@else

    {{-- Desktop / tablet table --}}
    <div class="lesson-card hidden overflow-x-auto md:block">
        <table class="admin-table w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Cover</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Title</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Category</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Price</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Created</th>
                    <th class="sticky top-0 bg-(--color-card) px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr class="border-t border-(--color-border) transition-colors hover:bg-black/[.02] dark:border-white/10 dark:hover:bg-white/[.03]">
                        <td class="px-4 py-3">
                            @if($course->cover_image_path)
                                <img src="{{ Storage::url($course->cover_image_path) }}" alt="{{ $course->title }}" class="h-10 w-14 rounded-lg object-cover">
                            @else
                                <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                                    <x-icon name="image" class="h-4 w-4" />
                                </div>
                            @endif
                        </td>
                        <td class="max-w-xs px-4 py-3">
                            <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</div>
                            <div class="truncate text-xs text-(--color-text-secondary)">{{ Str::limit($course->description, 55) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-(--color-primary)/10 px-2.5 py-1 text-xs font-semibold text-(--color-primary)">{{ $course->category->name }}</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-(--color-text) dark:text-white">{{ $course->price_mad }} MAD</td>
                        <td class="whitespace-nowrap px-4 py-3 text-xs text-(--color-text-secondary)">{{ $course->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.courses.lessons.index', $course) }}" title="Lessons"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                                    <x-icon name="video" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" title="Edit"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                      onsubmit="return confirm('Delete \'{{ $course->title }}\'?')">
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
        @foreach($courses as $course)
            <div class="lesson-card p-4">
                <div class="flex items-start gap-3">
                    @if($course->cover_image_path)
                        <img src="{{ Storage::url($course->cover_image_path) }}" alt="{{ $course->title }}" class="h-14 w-20 shrink-0 rounded-lg object-cover">
                    @else
                        <div class="flex h-14 w-20 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                            <x-icon name="image" class="h-5 w-5" />
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</div>
                        <span class="mt-1 inline-flex items-center rounded-full bg-(--color-primary)/10 px-2 py-0.5 text-[11px] font-semibold text-(--color-primary)">{{ $course->category->name }}</span>
                        <div class="mt-1 text-xs font-semibold text-(--color-text) dark:text-white">{{ $course->price_mad }} MAD</div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="btn-secondary flex-1 !py-1.5 text-xs">
                        <x-icon name="video" class="h-3.5 w-3.5" />
                        Lessons
                    </a>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn-secondary flex-1 !py-1.5 text-xs">
                        <x-icon name="edit" class="h-3.5 w-3.5" />
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                          onsubmit="return confirm('Delete \'{{ $course->title }}\'?')">
                        @csrf @method('DELETE')
                        <button class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-(--color-danger)/30 text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
@endif

@endsection
