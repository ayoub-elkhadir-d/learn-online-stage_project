@extends('layouts.app')

@section('title', $course->title.' - ArtiWeb')
@section('subtitle', $course->category->name)

@section('content')
<div class="mb-3 text-center">
    <h5 class="text-dark fw-bold mb-2">{{ $course->title }}</h5>
    <div class="text-muted">{{ $course->description }}</div>
</div>

<div class="auth-card p-4">
    @if(session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="text-muted" style="font-size:13px;">Price</div>
            <div class="h4 mb-0 fw-bold text-dark">{{ $course->price_mad }} MAD</div>
        </div>

        <form method="POST" action="{{ route('courses.purchase', $course->slug) }}" style="min-width: 260px;" class="text-center">
            @csrf
            <input type="hidden" name="payment_method" value="bank_transfer">
            <input type="hidden" name="reference" value="">
            <button class="btn btn-primary w-100" type="submit">
                <i class="fas fa-shopping-cart me-2"></i>Purchase
            </button>
            <small class="d-block text-muted mt-2">
                After purchase you’ll get access when Admin confirms payment.
            </small>
        </form>
    </div>

    <hr class="my-4" />

    <div class="text-muted">
        MVP note: Video content + quizzes will appear after purchase status becomes <b>paid</b>.
    </div>
</div>
@endsection

