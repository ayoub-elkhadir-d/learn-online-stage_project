@extends('admin.layout')
@section('title', isset($lesson) ? 'Edit Lesson' : 'Add Lesson')

@section('content')

<div class="mx-auto max-w-2xl">
    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Back to Lessons — {{ $course->title }}
    </a>

    <div class="lesson-card mt-4 overflow-hidden">
        <div class="flex items-center gap-3 bg-(--color-primary) px-6 py-4">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white">
                <x-icon name="video" class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-sm font-bold text-white">{{ isset($lesson) ? 'Edit Lesson' : 'Add Lesson' }}</h2>
                <p class="text-xs text-white/70">{{ $course->title }}</p>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div id="formAlert" class="mb-4 hidden rounded-xl border border-(--color-danger)/20 bg-(--color-danger)/10 px-4 py-3 text-sm text-(--color-danger)"></div>

            <form id="lessonForm" method="POST"
                  action="{{ isset($lesson) ? route('admin.courses.lessons.update', [$course, $lesson]) : route('admin.courses.lessons.store', $course) }}"
                  enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                @if(isset($lesson)) @method('PUT') @endif

                <div>
                    <label for="titleInput" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Lesson Title <span class="text-(--color-danger)">*</span></label>
                    <input type="text" name="title" id="titleInput" class="input-field"
                           value="{{ old('title', $lesson->title ?? '') }}" placeholder="e.g. Introduction to HTML">
                    <p class="mt-1.5 text-xs text-(--color-danger)" data-error-for="title">@error('title'){{ $message }}@enderror</p>
                </div>

                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Description</label>
                    <textarea name="description" id="description" rows="3" class="input-field resize-none"
                              placeholder="What will students learn in this lesson?">{{ old('description', $lesson->description ?? '') }}</textarea>
                    <p class="mt-1.5 text-xs text-(--color-danger)" data-error-for="description">@error('description'){{ $message }}@enderror</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                        Video File
                        @if(!isset($lesson)) <span class="text-(--color-danger)">*</span> @else <span class="font-normal text-(--color-text-secondary)">(leave empty to keep current)</span> @endif
                    </label>

                    <label for="videoInput" id="dropZone" class="flex cursor-pointer flex-col items-center gap-1.5 rounded-xl border-2 border-dashed border-(--color-border) px-6 py-8 text-center transition-colors hover:border-(--color-primary)/50 hover:bg-(--color-primary)/5 dark:border-white/15">
                        <div id="dropZoneEmpty" class="flex flex-col items-center gap-1.5">
                            <x-icon name="upload-cloud" class="h-8 w-8 text-(--color-text-secondary)" />
                            <div class="text-sm font-medium text-(--color-text) dark:text-white">Click to browse or drag a video here</div>
                            <div class="text-xs text-(--color-text-secondary)">MP4, WebM, OGV, MOV, AVI — max 200 MB</div>
                        </div>
                        <div id="dropZoneFile" class="hidden w-full items-center gap-3 text-left">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                                <x-icon name="video" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div id="fileName" class="truncate text-sm font-semibold text-(--color-text) dark:text-white"></div>
                                <div id="fileSize" class="text-xs text-(--color-text-secondary)"></div>
                            </div>
                            <button type="button" id="clearFile" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-(--color-border) text-(--color-text-secondary) hover:bg-black/5 dark:border-white/10 dark:hover:bg-white/10">
                                <x-icon name="x" class="h-4 w-4" />
                            </button>
                        </div>
                    </label>
                    <input type="file" name="video" id="videoInput" class="hidden"
                           accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo">
                    <p class="mt-1.5 text-xs text-(--color-danger)" data-error-for="video">@error('video'){{ $message }}@enderror</p>

                    @if(isset($lesson))
                        <div class="mt-2 flex items-center gap-2 rounded-lg bg-(--color-success)/10 px-3 py-2 text-xs text-(--color-success)">
                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                            <span>Current video: <strong>{{ basename($lesson->video_path) }}</strong></span>
                        </div>
                    @endif

                    {{-- Upload progress --}}
                    <div id="progressWrap" class="mt-3 hidden">
                        <div class="mb-1 flex items-center justify-between">
                            <span id="progressLabel" class="text-xs font-semibold text-(--color-primary)">Uploading…</span>
                            <span id="progressPercent" class="text-xs text-(--color-text-secondary)">0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-(--color-primary)/10">
                            <div id="progressBar" class="h-full rounded-full bg-(--color-primary) transition-[width] duration-150" style="width:0%"></div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="sort_order" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" min="0" class="input-field max-w-[140px]"
                           value="{{ old('sort_order', $lesson->sort_order ?? '') }}" placeholder="0">
                    <p class="mt-1.5 text-xs text-(--color-text-secondary)">Lower numbers appear first. Leave empty to append at the end.</p>
                    <p class="mt-1.5 text-xs text-(--color-danger)" data-error-for="sort_order">@error('sort_order'){{ $message }}@enderror</p>
                </div>

                <div class="mt-2 flex gap-3">
                    <button type="submit" id="submitBtn" class="btn-primary flex-1 sm:flex-none">
                        <x-icon name="check-circle" class="h-4 w-4" />
                        <span id="submitLabel">{{ isset($lesson) ? 'Update Lesson' : 'Add Lesson' }}</span>
                    </button>
                    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="btn-secondary flex-1 sm:flex-none">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('lessonForm');
    const dropZone = document.getElementById('dropZone');
    const videoInput = document.getElementById('videoInput');
    const dropZoneEmpty = document.getElementById('dropZoneEmpty');
    const dropZoneFile = document.getElementById('dropZoneFile');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const clearFile = document.getElementById('clearFile');
    const progressWrap = document.getElementById('progressWrap');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const progressLabel = document.getElementById('progressLabel');
    const submitBtn = document.getElementById('submitBtn');
    const submitLabel = document.getElementById('submitLabel');
    const formAlert = document.getElementById('formAlert');

    function formatBytes(bytes) {
        if (!bytes) return '0 B';
        const units = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + units[i];
    }

    function showFile(file) {
        dropZoneEmpty.classList.add('hidden');
        dropZoneFile.classList.remove('hidden');
        dropZoneFile.classList.add('flex');
        fileName.textContent = file.name;
        fileSize.textContent = formatBytes(file.size);
    }

    function resetDropZone() {
        videoInput.value = '';
        dropZoneEmpty.classList.remove('hidden');
        dropZoneFile.classList.add('hidden');
        dropZoneFile.classList.remove('flex');
    }

    videoInput.addEventListener('change', function () {
        if (videoInput.files.length) showFile(videoInput.files[0]);
    });

    clearFile.addEventListener('click', function (e) {
        e.preventDefault();
        resetDropZone();
    });

    ['dragover', 'dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (evt === 'dragover') {
                dropZone.classList.add('border-(--color-primary)', 'bg-(--color-primary)/5');
            } else {
                dropZone.classList.remove('border-(--color-primary)', 'bg-(--color-primary)/5');
            }
        });
    });

    dropZone.addEventListener('drop', function (e) {
        const files = e.dataTransfer.files;
        if (files.length) {
            videoInput.files = files;
            showFile(files[0]);
        }
    });

    function clearErrors() {
        formAlert.classList.add('hidden');
        formAlert.innerHTML = '';
        form.querySelectorAll('.is-invalid-admin').forEach(el => el.classList.remove('is-invalid-admin'));
        form.querySelectorAll('[data-error-for]').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        const messages = [];
        Object.keys(errors).forEach(field => {
            messages.push(errors[field][0]);
            const target = field === 'video' ? videoInput : form.querySelector(`[name="${field}"]`);
            if (target) target.classList.add('is-invalid-admin');
            const slot = form.querySelector(`[data-error-for="${field}"]`);
            if (slot) slot.textContent = errors[field][0];
        });
        formAlert.innerHTML = messages.join('<br>');
        formAlert.classList.remove('hidden');
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();

        xhr.open(form.method === 'GET' ? 'GET' : 'POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'pointer-events-none');
        submitLabel.textContent = 'Uploading…';
        progressWrap.classList.remove('hidden');

        xhr.upload.addEventListener('progress', function (e) {
            if (!e.lengthComputable) return;
            const pct = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = pct + '%';
            progressPercent.textContent = pct + '%';
            if (pct >= 100) {
                progressLabel.textContent = 'Processing…';
            }
        });

        xhr.addEventListener('load', function () {
            let response = {};
            try { response = JSON.parse(xhr.responseText); } catch (e) {}

            if (xhr.status >= 200 && xhr.status < 300) {
                progressLabel.textContent = 'Done!';
                progressBar.style.width = '100%';
                progressPercent.textContent = '100%';
                submitLabel.textContent = 'Saved ✓';
                window.location.href = response.redirect || '{{ route('admin.courses.lessons.index', $course) }}';
                return;
            }

            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'pointer-events-none');
            submitLabel.textContent = '{{ isset($lesson) ? "Update Lesson" : "Add Lesson" }}';
            progressWrap.classList.add('hidden');

            if (xhr.status === 422 && response.errors) {
                showErrors(response.errors);
            } else {
                formAlert.innerHTML = 'Something went wrong. Please try again.';
                formAlert.classList.remove('hidden');
            }
        });

        xhr.addEventListener('error', function () {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'pointer-events-none');
            submitLabel.textContent = '{{ isset($lesson) ? "Update Lesson" : "Add Lesson" }}';
            progressWrap.classList.add('hidden');
            formAlert.innerHTML = 'Upload failed. Check your connection and try again.';
            formAlert.classList.remove('hidden');
        });

        xhr.send(formData);
    });
})();
</script>
@endpush

@endsection
