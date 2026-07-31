@extends('admin.layout')
@section('title', 'Categories')

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-(--color-text) dark:text-white">All Categories</h2>
        <p class="text-sm text-(--color-text-secondary)">{{ $categories->total() }} {{ Str::plural('category', $categories->total()) }} total</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <x-icon name="tag" class="h-4 w-4" />
        Add Category
    </a>
</div>

<form method="GET" action="{{ route('admin.categories.index') }}" class="mb-5">
    <div class="relative max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search categories..."
               class="input-field pl-9">
    </div>
</form>

@if($categories->isEmpty())
    <div class="lesson-card flex flex-col items-center px-6 py-16 text-center">
        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
            <x-icon name="tag" class="h-7 w-7" />
        </span>
        <h3 class="text-base font-bold text-(--color-text) dark:text-white">No categories found</h3>
        <p class="mt-1 max-w-sm text-sm text-(--color-text-secondary)">
            @if(request('q')) Try a different search term. @else Create your first category to start organizing courses. @endif
        </p>
        @unless(request('q'))
            <a href="{{ route('admin.categories.create') }}" class="btn-primary mt-5">
                <x-icon name="tag" class="h-4 w-4" />
                Add Category
            </a>
        @endunless
    </div>
@else

    {{-- Desktop / tablet table --}}
    <div class="lesson-card hidden overflow-x-auto md:block">
        <table class="admin-table w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="sticky top-0 bg-(--color-card) px-5 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Name</th>
                    <th class="sticky top-0 bg-(--color-card) px-5 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Slug</th>
                    <th class="sticky top-0 bg-(--color-card) px-5 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Courses</th>
                    <th class="sticky top-0 bg-(--color-card) px-5 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark) text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr class="border-t border-(--color-border) transition-colors hover:bg-black/[.02] dark:border-white/10 dark:hover:bg-white/[.03]">
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-(--color-text) dark:text-white">{{ $category->name }}</div>
                            @if($category->description)
                                <div class="mt-0.5 max-w-xs truncate text-xs text-(--color-text-secondary)">{{ $category->description }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <code class="rounded-md bg-(--color-text)/8 px-2 py-1 text-xs text-(--color-text-secondary) dark:bg-white/10">{{ $category->slug }}</code>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1 rounded-full bg-(--color-primary)/10 px-2.5 py-1 text-xs font-semibold text-(--color-primary)">
                                <x-icon name="book-open" class="h-3 w-3" />
                                {{ $category->courses_count }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn-secondary !px-3 !py-1.5 text-xs">
                                    <x-icon name="edit" class="h-3.5 w-3.5" />
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete category \'{{ $category->name }}\'?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                        <x-icon name="x" class="h-3.5 w-3.5" />
                                        Delete
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
        @foreach($categories as $category)
            <div class="lesson-card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-(--color-text) dark:text-white">{{ $category->name }}</div>
                        <code class="mt-1 inline-block rounded-md bg-(--color-text)/8 px-2 py-0.5 text-xs text-(--color-text-secondary) dark:bg-white/10">{{ $category->slug }}</code>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-(--color-primary)/10 px-2.5 py-1 text-xs font-semibold text-(--color-primary)">
                        <x-icon name="book-open" class="h-3 w-3" />
                        {{ $category->courses_count }}
                    </span>
                </div>
                @if($category->description)
                    <p class="mt-2 text-xs text-(--color-text-secondary)">{{ Str::limit($category->description, 100) }}</p>
                @endif
                <div class="mt-3 flex items-center gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-secondary flex-1 !py-1.5 text-xs">
                        <x-icon name="edit" class="h-3.5 w-3.5" />
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="flex-1"
                          onsubmit="return confirm('Delete category \'{{ $category->name }}\'?')">
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

    <div class="mt-6">{{ $categories->links() }}</div>
@endif

@endsection
