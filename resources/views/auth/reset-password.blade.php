@extends('layouts.app')
@section('title', 'Set New Password - LearnHub')
@section('subtitle', 'Create a secure password')
@section('content')
<div class="text-center mb-4">
    <div class="mb-3">
        <i class="fas fa-shield-alt fa-3x text-success"></i>
    </div>
    <h5 class="text-dark mb-2">Set New Password</h5>
    <p class="text-muted">Choose a strong password for your account</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-envelope me-2"></i>Email Address
        </label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email', request()->email) }}" 
               placeholder="Confirm your email" required readonly>
        @error('email')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-lock me-2"></i>New Password
        </label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" placeholder="Enter new password" required>
        @error('password')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
        <small class="form-text text-muted mt-2">
            <i class="fas fa-info-circle me-1"></i>Use at least 8 characters with mix of letters and numbers
        </small>
    </div>
    
    <div class="mb-4">
        <label class="form-label">
            <i class="fas fa-lock me-2"></i>Confirm New Password
        </label>
        <input type="password" class="form-control" 
               name="password_confirmation" 
               placeholder="Confirm new password" required>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 mb-4">
        <i class="fas fa-check me-2"></i>Update Password
    </button>
</form>
@endsection