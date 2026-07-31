@extends('admin.layout')
@section('title', isset($category) ? 'Edit Category' : 'Add Category')

@section('content')

<div class="mx-auto max-w-xl">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Back to Categories
    </a>

    <div class="lesson-card mt-4 p-6 sm:p-8">
        <h2 class="text-base font-bold text-(--color-text) dark:text-white">{{ isset($category) ? 'Edit Category' : 'New Category' }}</h2>
        <p class="mt-1 text-sm text-(--color-text-secondary)">
            {{ isset($category) ? 'Update the details for '.$category->name : 'Categories help students browse courses by topic.' }}
        </p>

        <form method="POST"
              action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
              class="mt-6 flex flex-col gap-4">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Name <span class="text-(--color-danger)">*</span></label>
                <input type="text" id="name" name="name" data-slug-source
                       value="{{ old('name', $category->name ?? '') }}"
                       placeholder="e.g. Web Development" required
                       class="input-field @error('name') border-(--color-danger)! @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                    Slug <span class="font-normal text-(--color-text-secondary)">(auto-generated, editable)</span>
                </label>
                <input type="text" id="slug" name="slug" data-slug-target
                       value="{{ old('slug', $category->slug ?? '') }}"
                       placeholder="web-development"
                       class="input-field font-mono text-xs @error('slug') border-(--color-danger)! @enderror">
                @error('slug')
                    <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                    Description <span class="font-normal text-(--color-text-secondary)">(optional)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="What kind of courses belong in this category?"
                          class="input-field resize-none @error('description') border-(--color-danger)! @enderror">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-2 flex gap-3">
                <button type="submit" class="btn-primary flex-1 sm:flex-none">
                    <x-icon name="check-circle" class="h-4 w-4" />
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary flex-1 sm:flex-none">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var source = document.querySelector('[data-slug-source]');
    var target = document.querySelector('[data-slug-target]');
    if (!source || !target) return;

    var touched = target.value.trim().length > 0;
    target.addEventListener('input', function () { touched = true; });

    source.addEventListener('input', function () {
        if (touched) return;
        target.value = source.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    });
})();
</script>
@endpush

@endsection
