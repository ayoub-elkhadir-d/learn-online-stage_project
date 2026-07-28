@extends('admin.layout')
@section('title', isset($lesson) ? 'Edit Lesson' : 'Add Lesson')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="text-muted text-decoration-none" style="font-size:13px;">
        <i class="fas fa-arrow-left me-1"></i>Back to Lessons — {{ $course->title }}
    </a>
</div>

<div class="card p-0 overflow-hidden" style="max-width:720px;">
    <div class="px-4 py-3 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
        <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:rgba(255,255,255,.15);">
            <i class="fas fa-clapperboard text-white" style="font-size:18px;"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0 text-white">{{ isset($lesson) ? 'Edit Lesson' : 'Add Lesson' }}</h5>
            <small style="color:rgba(255,255,255,.7);font-size:12.5px;">{{ $course->title }}</small>
        </div>
    </div>

    <div class="p-4">
        <div id="formAlert" class="alert alert-danger border-0 d-none" style="border-radius:12px;font-size:13.5px;"></div>

        <form id="lessonForm" method="POST"
              action="{{ isset($lesson)
                  ? route('admin.courses.lessons.update', [$course, $lesson])
                  : route('admin.courses.lessons.store', $course) }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($lesson)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Lesson Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $lesson->title ?? '') }}" placeholder="e.g. Introduction to HTML">
                <div class="invalid-feedback" data-error-for="title">@error('title'){{ $message }}@enderror</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                          placeholder="What will students learn in this lesson?">{{ old('description', $lesson->description ?? '') }}</textarea>
                <div class="invalid-feedback" data-error-for="description">@error('description'){{ $message }}@enderror</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size:13px;">
                    Video File
                    @if(!isset($lesson)) <span class="text-danger">*</span> @else <span class="text-muted fw-normal">(leave empty to keep current)</span> @endif
                </label>

                <label for="videoInput" id="dropZone" class="d-block text-center" style="border:2px dashed #d8d5f5;border-radius:14px;padding:2rem 1rem;cursor:pointer;background:#faf9ff;transition:all .2s;">
                    <div id="dropZoneEmpty">
                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width:52px;height:52px;background:#ede9fe;">
                            <i class="fas fa-cloud-upload-alt" style="font-size:22px;color:#7c3aed;"></i>
                        </div>
                        <div class="fw-semibold" style="font-size:14px;color:#1e1b4b;">Click to browse or drag a video here</div>
                        <div class="text-muted mt-1" style="font-size:12px;">MP4, WebM, OGV, MOV, AVI — max 200 MB</div>
                    </div>
                    <div id="dropZoneFile" class="d-none align-items-center gap-3 text-start">
                        <div class="d-flex align-items-center justify-content-center rounded-3" style="width:44px;height:44px;background:#ede9fe;flex-shrink:0;">
                            <i class="fas fa-file-video" style="font-size:18px;color:#7c3aed;"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate" id="fileName" style="font-size:13.5px;color:#1e1b4b;"></div>
                            <div class="text-muted" id="fileSize" style="font-size:11.5px;"></div>
                        </div>
                        <button type="button" id="clearFile" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </label>
                <input type="file" name="video" id="videoInput" class="d-none @error('video') is-invalid @enderror"
                       accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo">
                <div class="invalid-feedback d-block" data-error-for="video">@error('video'){{ $message }}@enderror</div>

                @if(isset($lesson))
                <div class="mt-2 p-2 rounded-3 d-flex align-items-center gap-2" style="background:#f0fdf4;font-size:12px;color:#065f46;">
                    <i class="fas fa-check-circle"></i>
                    <span>Current video: <strong>{{ basename($lesson->video_path) }}</strong></span>
                </div>
                @endif

                {{-- Upload progress --}}
                <div id="progressWrap" class="mt-3 d-none">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;color:#4f46e5;font-weight:600;" id="progressLabel">Uploading…</span>
                        <span style="font-size:12px;color:#6b7280;" id="progressPercent">0%</span>
                    </div>
                    <div class="rounded-pill overflow-hidden" style="height:8px;background:#ede9fe;">
                        <div id="progressBar" class="h-100 rounded-pill" style="width:0%;background:linear-gradient(135deg,#4f46e5,#7c3aed);transition:width .15s;"></div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Sort Order</label>
                <input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
                       value="{{ old('sort_order', $lesson->sort_order ?? '') }}" placeholder="0" style="max-width:120px;">
                <div class="form-text">Lower numbers appear first. Leave empty to append at the end.</div>
                <div class="invalid-feedback" data-error-for="sort_order">@error('sort_order'){{ $message }}@enderror</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i><span id="submitLabel">{{ isset($lesson) ? 'Update Lesson' : 'Add Lesson' }}</span>
                </button>
                <a href="{{ route('admin.courses.lessons.index', $course) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

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
        dropZoneEmpty.classList.add('d-none');
        dropZoneFile.classList.remove('d-none');
        dropZoneFile.classList.add('d-flex');
        fileName.textContent = file.name;
        fileSize.textContent = formatBytes(file.size);
    }

    function resetDropZone() {
        videoInput.value = '';
        dropZoneEmpty.classList.remove('d-none');
        dropZoneFile.classList.add('d-none');
        dropZoneFile.classList.remove('d-flex');
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
                dropZone.style.borderColor = '#7c3aed';
                dropZone.style.background = '#f3f0ff';
            } else {
                dropZone.style.borderColor = '#d8d5f5';
                dropZone.style.background = '#faf9ff';
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
        formAlert.classList.add('d-none');
        formAlert.innerHTML = '';
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-error-for]').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        const messages = [];
        Object.keys(errors).forEach(field => {
            messages.push(errors[field][0]);
            const target = field === 'video' ? videoInput : form.querySelector(`[name="${field}"]`);
            if (target) target.classList.add('is-invalid');
            const slot = form.querySelector(`[data-error-for="${field}"]`);
            if (slot) slot.textContent = errors[field][0];
        });
        formAlert.innerHTML = '<i class="fas fa-triangle-exclamation me-2"></i>' + messages.join('<br>');
        formAlert.classList.remove('d-none');
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
        submitLabel.textContent = 'Uploading…';
        progressWrap.classList.remove('d-none');

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
            submitLabel.textContent = '{{ isset($lesson) ? "Update Lesson" : "Add Lesson" }}';
            progressWrap.classList.add('d-none');

            if (xhr.status === 422 && response.errors) {
                showErrors(response.errors);
            } else {
                formAlert.innerHTML = '<i class="fas fa-triangle-exclamation me-2"></i>Something went wrong. Please try again.';
                formAlert.classList.remove('d-none');
            }
        });

        xhr.addEventListener('error', function () {
            submitBtn.disabled = false;
            submitLabel.textContent = '{{ isset($lesson) ? "Update Lesson" : "Add Lesson" }}';
            progressWrap.classList.add('d-none');
            formAlert.innerHTML = '<i class="fas fa-triangle-exclamation me-2"></i>Upload failed. Check your connection and try again.';
            formAlert.classList.remove('d-none');
        });

        xhr.send(formData);
    });
})();
</script>
@endsection
