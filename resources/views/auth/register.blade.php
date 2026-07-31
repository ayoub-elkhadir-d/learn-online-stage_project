@extends('layouts.app')

@section('title', 'Create Account — ArtiWeb')
@section('heading', 'Create your account')
@section('subtitle', 'Start learning in-demand skills today')

@section('content')
<form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
    @csrf

    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Full Name</label>
        <div class="relative">
            <x-icon name="user" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Your full name" required autofocus
                   class="input-field pl-9 @error('name') border-(--color-danger)! @enderror">
        </div>
        @error('name')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Email Address</label>
        <div class="relative">
            <x-icon name="mail" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required
                   class="input-field pl-9 @error('email') border-(--color-danger)! @enderror">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Password</label>
        <div class="relative">
            <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required
                   class="input-field pl-9 @error('password') border-(--color-danger)! @enderror">
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Confirm Password</label>
        <div class="relative">
            <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" required
                   class="input-field pl-9">
        </div>
    </div>

    <button type="submit" class="btn-primary mt-2 w-full">
        Create Account
        <x-icon name="arrow-right" class="h-4 w-4" />
    </button>
</form>

<div class="my-6 flex items-center gap-3 text-xs text-(--color-text-secondary)">
    <div class="h-px flex-1 bg-(--color-border) dark:bg-white/10"></div>
    <span>Already have an account?</span>
    <div class="h-px flex-1 bg-(--color-border) dark:bg-white/10"></div>
</div>

<a href="{{ route('login') }}" class="btn-secondary w-full">
    Sign in instead
</a>
@endsection
