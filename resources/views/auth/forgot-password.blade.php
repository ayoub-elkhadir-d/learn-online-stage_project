@extends('layouts.app')
@section('title', 'Reset Password - LearnHub')
@section('subtitle', 'We\'ll help you get back in')
@section('content')
<div class="text-center mb-4">
    <div class="mb-3">
        <i class="fas fa-key fa-3x text-primary"></i>
    </div>
    <h5 class="text-dark mb-2">Forgot Your Password?</h5>
    <p class="text-muted">Enter your email and we'll send you a reset link</p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-4">
        <label class="form-label">
            <i class="fas fa-envelope me-2"></i>Email Address
        </label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email') }}" 
               placeholder="Enter your registered email" required>
        @error('email')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-primary w-100 mb-4">
        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
    </button>
</form>

<div class="text-center">
    <a href="{{ route('login') }}" class="auth-link">
        <i class="fas fa-arrow-left me-2"></i>Back to Login
    </a>
</div>
@endsection