@extends('layouts.app')

@section('title', 'Forgot Password — ArtiWeb')
@section('heading', 'Forgot your password?')
@section('subtitle', 'Enter your email and we\'ll send you a reset link')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
    @csrf

    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Email Address</label>
        <div class="relative">
            <x-icon name="mail" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Your registered email" required autofocus
                   class="input-field pl-9 @error('email') border-(--color-danger)! @enderror">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary mt-2 w-full">
        Send Reset Link
        <x-icon name="arrow-right" class="h-4 w-4" />
    </button>
</form>

<a href="{{ route('login') }}" class="mt-6 flex items-center justify-center gap-1.5 text-sm font-medium text-(--color-primary) transition-colors hover:text-(--color-primary-dark)">
    <x-icon name="chevron-left" class="h-4 w-4" />
    Back to login
</a>
@endsection
