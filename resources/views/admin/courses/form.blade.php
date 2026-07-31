@extends('admin.layout')
@section('title', isset($course) ? 'Edit Course' : 'Add Course')

@section('content')

<div class="mx-auto max-w-2xl">
    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Back to Courses
    </a>

    <div class="lesson-card mt-4 p-6 sm:p-8">
        <h2 class="text-base font-bold text-(--color-text) dark:text-white">{{ isset($course) ? 'Edit Course' : 'New Course' }}</h2>
        <p class="mt-1 text-sm text-(--color-text-secondary)">
            {{ isset($course) ? 'Update the details for: '.$course->title : 'Fill in the details to create a new course.' }}
        </p>

        <form method="POST"
              action="{{ isset($course) ? route('admin.courses.update', $course) : route('admin.courses.store') }}"
              enctype="multipart/form-data" class="mt-6 flex flex-col gap-4">
            @csrf
            @if(isset($course)) @method('PUT') @endif

            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Title <span class="text-(--color-danger)">*</span></label>
                <input type="text" id="title" name="title"
                       value="{{ old('title', $course->title ?? '') }}"
                       placeholder="e.g. Complete Web Development Bootcamp" required
                       class="input-field @error('title') border-(--color-danger)! @enderror">
                @error('title')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Description</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Describe what students will learn in this course..."
                          class="input-field resize-none @error('description') border-(--color-danger)! @enderror">{{ old('description', $course->description ?? '') }}</textarea>
                @error('description')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Category <span class="text-(--color-danger)">*</span></label>
                    <select id="category_id" name="category_id" required
                            class="input-field @error('category_id') border-(--color-danger)! @enderror">
                        <option value="">— Select category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                    <a href="{{ route('admin.categories.create') }}" class="mt-1.5 inline-block text-xs font-medium text-(--color-primary) hover:text-(--color-primary-dark)">+ Add a new category</a>
                </div>
                <div>
                    <label for="price_mad" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Price (MAD) <span class="text-(--color-danger)">*</span></label>
                    <input type="number" id="price_mad" name="price_mad" min="0" required
                           value="{{ old('price_mad', $course->price_mad ?? 250) }}"
                           class="input-field @error('price_mad') border-(--color-danger)! @enderror">
                    @error('price_mad')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Cover Image</label>

                <div id="coverArea" data-cover-area
                     class="relative flex cursor-pointer flex-col items-center justify-center gap-1.5 overflow-hidden rounded-xl border-2 border-dashed border-(--color-border) px-6 py-6 text-center transition-colors hover:border-(--color-primary)/50 hover:bg-(--color-primary)/5 dark:border-white/15">

                    <img data-cover-preview
                         @if(isset($course) && $course->cover_image_path) src="{{ Storage::url($course->cover_image_path) }}" @endif
                         class="{{ isset($course) && $course->cover_image_path ? '' : 'hidden' }} h-32 w-full rounded-lg object-cover" alt="Cover preview">

                    <div data-cover-placeholder class="{{ isset($course) && $course->cover_image_path ? 'hidden' : '' }} flex flex-col items-center gap-1.5">
                        <x-icon name="upload-cloud" class="h-7 w-7 text-(--color-text-secondary)" />
                        <div class="text-sm font-medium text-(--color-text) dark:text-white">Click or drag & drop to upload</div>
                        <div class="text-xs text-(--color-text-secondary)">JPG, PNG or WEBP — Max 2MB</div>
                    </div>

                    <span data-cover-replace-hint class="{{ isset($course) && $course->cover_image_path ? '' : 'hidden' }} text-xs font-medium text-(--color-text-secondary)">Click to replace</span>
                </div>
                <input type="file" name="cover_image" id="coverInput" class="hidden" accept="image/*">
                @error('cover_image')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
            </div>

            <div class="mt-2 flex gap-3">
                <button type="submit" class="btn-primary flex-1 sm:flex-none">
                    <x-icon name="check-circle" class="h-4 w-4" />
                    {{ isset($course) ? 'Update Course' : 'Create Course' }}
                </button>
                <a href="{{ route('admin.courses.index') }}" class="btn-secondary flex-1 sm:flex-none">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var input = document.getElementById('coverInput');
    var area = document.querySelector('[data-cover-area]');
    if (!input || !area) return;

    var preview = area.querySelector('[data-cover-preview]');
    var placeholder = area.querySelector('[data-cover-placeholder]');
    var replaceHint = area.querySelector('[data-cover-replace-hint]');

    area.addEventListener('click', function () { input.click(); });

    input.addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            replaceHint.classList.remove('hidden');
        };
        reader.readAsDataURL(this.files[0]);
    });

    ['dragenter', 'dragover'].forEach(function (evt) {
        area.addEventListener(evt, function (e) {
            e.preventDefault();
            area.classList.add('border-(--color-primary)', 'bg-(--color-primary)/5');
        });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
        area.addEventListener(evt, function (e) {
            e.preventDefault();
            area.classList.remove('border-(--color-primary)', 'bg-(--color-primary)/5');
        });
    });
    area.addEventListener('drop', function (e) {
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        }
    });
})();
</script>
@endpush

@endsection
