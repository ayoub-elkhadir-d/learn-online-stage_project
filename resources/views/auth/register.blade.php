@extends('layouts.app')
@section('title', 'Create Account - ArtiWeb')
@section('subtitle', 'Join the ArtiWeb community')
@section('content')
<div class="text-center mb-2">
    <h6 class="text-dark mb-1 fw-bold">Create Your Account</h6>
    <small class="text-muted">Start building amazing websites</small>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-2">
        <label class="form-label">
            <i class="fas fa-user me-2"></i>Full Name
        </label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" 
               name="name" value="{{ old('name') }}" 
               placeholder="Enter your full name" required>
        @error('name')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="mb-2">
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
    
    <div class="mb-2">
        <label class="form-label">
            <i class="fas fa-lock me-2"></i>Password
        </label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" placeholder="Choose a strong password" required>
        @error('password')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-lock me-2"></i>Confirm Password
        </label>
        <input type="password" class="form-control" 
               name="password_confirmation" 
               placeholder="Confirm your password" required>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="fas fa-user-plus me-2"></i>Create Account
    </button>
</form>

<div class="divider">
    <span>Already have an account?</span>
</div>

<div class="text-center mt-2">
    <a href="{{ route('login') }}" class="auth-link">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In Here
    </a>
</div>
@endsection
