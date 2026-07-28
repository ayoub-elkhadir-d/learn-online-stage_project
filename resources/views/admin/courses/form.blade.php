@extends('admin.layout')
@section('title', isset($course) ? 'Edit Course' : 'Add Course')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">

        <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i>Back to Courses
        </a>

        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">{{ isset($course) ? 'Edit Course' : 'New Course' }}</h6>
                <p class="text-muted mb-4" style="font-size:13px;">
                    {{ isset($course) ? 'Update the details for: '.$course->title : 'Fill in the details to create a new course.' }}
                </p>

                <form method="POST"
                      action="{{ isset($course) ? route('admin.courses.update', $course) : route('admin.courses.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($course)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $course->title ?? '') }}"
                               placeholder="e.g. Complete Web Development Bootcamp"
                               required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe what students will learn in this course...">{{ old('description', $course->description ?? '') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:13px;">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">— Select category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $course->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Price (MAD) <span class="text-danger">*</span></label>
                            <input type="number" name="price_mad"
                                   class="form-control @error('price_mad') is-invalid @enderror"
                                   value="{{ old('price_mad', $course->price_mad ?? 250) }}"
                                   min="0" required>
                            @error('price_mad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Cover Image</label>
                        @if(isset($course) && $course->cover_image_path)
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <img src="{{ Storage::url($course->cover_image_path) }}"
                                     style="height:70px;border-radius:10px;object-fit:cover;">
                                <small class="text-muted">Upload a new image to replace</small>
                            </div>
                        @endif
                        <input type="file" name="cover_image"
                               class="form-control @error('cover_image') is-invalid @enderror"
                               accept="image/*">
                        <div class="form-text">Max 2MB — JPG, PNG, WEBP</div>
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>{{ isset($course) ? 'Update Course' : 'Create Course' }}
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
