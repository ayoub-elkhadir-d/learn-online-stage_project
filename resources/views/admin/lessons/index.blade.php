@extends('admin.layout')
@section('title', 'Lessons — ' . $course->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.courses.index') }}" class="text-muted text-decoration-none" style="font-size:13px;">
            <i class="fas fa-arrow-left me-1"></i>Back to Courses
        </a>
        <h5 class="fw-bold mb-0 mt-1">{{ $course->title }}</h5>
        <small class="text-muted">{{ $lessons->count() }} lesson(s)</small>
    </div>
    <a href="{{ route('admin.courses.lessons.create', $course) }}" class="btn btn-primary btn-sm px-4">
        <i class="fas fa-plus me-1"></i>Add Lesson
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width:50px;">Order</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Video</th>
                    <th class="pe-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                <tr>
                    <td class="ps-4 text-center">
                        <span class="badge rounded-pill" style="background:#ede9fe;color:#6d28d9;">{{ $lesson->sort_order }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:14px;">{{ $lesson->title }}</div>
                    </td>
                    <td>
                        <div class="text-muted" style="font-size:12px;">{{ Str::limit($lesson->description, 60) ?: '—' }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background:#d1fae5;color:#065f46;font-size:11px;">
                            <i class="fas fa-video me-1"></i>Uploaded
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}"
                              class="d-inline" onsubmit="return confirm('Delete \'{{ $lesson->title }}\'? The video file will also be removed.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-video fa-2x mb-2 d-block" style="color:#a78bfa;"></i>
                        <span class="text-muted">No lessons yet.</span>
                        <a href="{{ route('admin.courses.lessons.create', $course) }}" class="d-block mt-2 text-decoration-none" style="color:#4f46e5;font-size:13px;">
                            Add your first lesson →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
