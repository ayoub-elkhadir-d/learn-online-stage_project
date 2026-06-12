@extends('layouts.app')
@section('title', 'Welcome Back - LearnHub')
@section('subtitle', 'Sign in to continue learning')
@section('content')
<div class="text-center mb-4">
    <h5 class="text-dark mb-2">Welcome Back!</h5>
    <p class="text-muted">Sign in to access your courses</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-envelope me-2"></i>Email Address
        </label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email') }}" 
               placeholder="Enter your email address" required>
        @error('email')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-lock me-2"></i>Password
        </label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" placeholder="Enter your password" required>
        @error('password')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                <i class="fas fa-heart me-1"></i>Remember me
            </label>
        </div>
        <a href="{{ route('password.request') }}" class="auth-link">
            <i class="fas fa-key me-1"></i>Forgot Password?
        </a>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 mb-4">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In
    </button>
</form>

<div class="divider">
    <span>New to LearnHub?</span>
</div>

<div class="text-center">
    <a href="{{ route('register') }}" class="auth-link">
        <i class="fas fa-user-plus me-2"></i>Create Account
    </a>
</div>
@endsection