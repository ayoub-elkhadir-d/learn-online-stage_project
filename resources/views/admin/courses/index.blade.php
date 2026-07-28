@extends('admin.layout')
@section('title', 'Courses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">All Courses</h5>
        <small class="text-muted">{{ $courses->total() }} course(s) total</small>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm px-4">
        <i class="fas fa-plus me-1"></i>Add Course
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width:40px;">#</th>
                    <th style="width:60px;">Cover</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Created</th>
                    <th class="pe-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                <tr>
                    <td class="ps-4 text-muted" style="font-size:13px;">{{ $course->id }}</td>
                    <td>
                        @if($course->cover_image_path)
                            <img src="{{ Storage::url($course->cover_image_path) }}"
                                 style="width:50px;height:38px;object-fit:cover;border-radius:8px;">
                        @else
                            <div style="width:50px;height:38px;background:#ede9fe;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-image" style="color:#a78bfa;font-size:14px;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:14px;">{{ $course->title }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ Str::limit($course->description, 55) }}</div>
                    </td>
                    <td><span class="badge-category">{{ $course->category->name }}</span></td>
                    <td class="fw-semibold" style="color:#4f46e5;">{{ $course->price_mad }} MAD</td>
                    <td class="text-muted" style="font-size:13px;">{{ $course->created_at->format('d M Y') }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('admin.courses.lessons.index', $course) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="fas fa-video me-1"></i>Lessons
                        </a>
                        <a href="{{ route('admin.courses.edit', $course) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="d-inline"
                              onsubmit="return confirm('Delete \'{{ $course->title }}\'?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-book-open fa-2x mb-2 d-block" style="color:#a78bfa;"></i>
                        <span class="text-muted">No courses yet.</span>
                        <a href="{{ route('admin.courses.create') }}" class="d-block mt-2 text-decoration-none" style="color:#4f46e5;font-size:13px;">
                            Add your first course →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $courses->links() }}</div>
@endsection
