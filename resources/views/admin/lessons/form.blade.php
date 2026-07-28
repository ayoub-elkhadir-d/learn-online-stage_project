@extends('admin.layout')
@section('title', isset($lesson) ? 'Edit Lesson' : 'Add Lesson')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="text-muted text-decoration-none" style="font-size:13px;">
        <i class="fas fa-arrow-left me-1"></i>Back to Lessons — {{ $course->title }}
    </a>
</div>

<div class="card p-4" style="max-width:680px;">
    <h5 class="fw-bold mb-4">{{ isset($lesson) ? 'Edit Lesson' : 'Add Lesson' }}</h5>

    <form method="POST"
          action="{{ isset($lesson)
              ? route('admin.courses.lessons.update', [$course, $lesson])
              : route('admin.courses.lessons.store', $course) }}"
          enctype="multipart/form-data">
        @csrf
        @if(isset($lesson)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px;">Lesson Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $lesson->title ?? '') }}" placeholder="e.g. Introduction to HTML">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                      placeholder="What will students learn in this lesson?">{{ old('description', $lesson->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px;">
                Video File
                @if(!isset($lesson)) <span class="text-danger">*</span> @else <span class="text-muted fw-normal">(leave empty to keep current)</span> @endif
            </label>
            <input type="file" name="video" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo"
                   class="form-control @error('video') is-invalid @enderror">
            <div class="form-text">Accepted: MP4, WebM, OGV, MOV, AVI — max 200 MB</div>
            @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror

            @if(isset($lesson))
            <div class="mt-2 p-2 rounded-3 d-flex align-items-center gap-2" style="background:#f0fdf4;font-size:12px;color:#065f46;">
                <i class="fas fa-check-circle"></i>
                <span>Current video: <strong>{{ basename($lesson->video_path) }}</strong></span>
            </div>
            @endif
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;">Sort Order</label>
            <input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $lesson->sort_order ?? '') }}" placeholder="0" style="max-width:120px;">
            <div class="form-text">Lower numbers appear first. Leave empty to append at the end.</div>
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i>{{ isset($lesson) ? 'Update Lesson' : 'Add Lesson' }}
            </button>
            <a href="{{ route('admin.courses.lessons.index', $course) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
