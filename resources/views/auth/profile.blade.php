@extends('layouts.app')
@section('title', 'My Profile - LearnHub')
@section('subtitle', 'Manage your account settings')
@section('content')
<div class="text-center mb-4">
    <div class="mb-3">
        <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
             style="width: 80px; height: 80px;">
            <i class="fas fa-user fa-2x text-white"></i>
        </div>
    </div>
    <h5 class="text-dark mb-2">{{ auth()->user()->name }}</h5>
    <p class="text-muted">Update your profile information</p>
</div>

<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-user me-2"></i>Full Name
        </label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" 
               name="name" value="{{ old('name', auth()->user()->name) }}" 
               placeholder="Enter your full name" required>
        @error('name')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="mb-4">
        <label class="form-label">
            <i class="fas fa-envelope me-2"></i>Email Address
        </label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email', auth()->user()->email) }}" 
               placeholder="Enter your email address" required>
        @error('email')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>
    
    <div class="border-top pt-4 mb-4">
        <h6 class="text-dark mb-3">
            <i class="fas fa-key me-2"></i>Change Password (Optional)
        </h6>
        
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" placeholder="Leave blank to keep current password">
            @error('password')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" 
                   name="password_confirmation" 
                   placeholder="Confirm your new password">
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 mb-4">
        <i class="fas fa-save me-2"></i>Update Profile
    </button>
</form>

<div class="d-flex justify-content-between">
    <a href="{{ route('dashboard') }}" class="auth-link">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-sign-out-alt me-2"></i>Sign Out
        </button>
    </form>
</div>
@endsection