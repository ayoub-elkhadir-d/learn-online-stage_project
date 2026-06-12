@extends('layouts.app')

@section('title', 'Courses - ArtiWeb')
@section('subtitle', 'Browse training courses')

@section('content')
<div class="text-center mb-4">
    <h5 class="text-dark fw-bold mb-2">Course Catalog</h5>
    <small class="text-muted">Pick a category and choose your course</small>
</div>

@foreach($categories as $category)
    <div class="mb-5">
        <h6 class="fw-bold text-dark mb-3">{{ $category->name }}</h6>
        <div class="row g-3">
            @forelse($category->courses as $course)
                <div class="col-lg-4 col-md-6">
                    <div class="auth-card p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold text-dark">{{ $course->title }}</div>
                                <div class="text-muted" style="font-size:13px;">{{ $course->description }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark">{{ $course->price_mad }} MAD</div>
                                <a href="{{ route('courses.show', $course->slug) }}" class="auth-link">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">No courses yet.</div>
            @endforelse
        </div>
    </div>
@endforeach
@endsection

