@extends('layouts.app')

@section('title', 'Reset Password — ArtiWeb')
@section('heading', 'Set a new password')
@section('subtitle', 'Choose a strong password for your account')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Email Address</label>
        <div class="relative">
            <x-icon name="mail" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="email" id="email" name="email" value="{{ old('email', request()->email) }}" required readonly
                   class="input-field pl-9 cursor-not-allowed opacity-70 @error('email') border-(--color-danger)! @enderror">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">New Password</label>
        <div class="relative">
            <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required
                   class="input-field pl-9 @error('password') border-(--color-danger)! @enderror">
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
        <p class="mt-1.5 text-xs text-(--color-text-secondary)">Use at least 8 characters with a mix of letters and numbers.</p>
    </div>

    <div>
        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Confirm New Password</label>
        <div class="relative">
            <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password" required
                   class="input-field pl-9">
        </div>
    </div>

    <button type="submit" class="btn-primary mt-2 w-full">
        <x-icon name="check-circle" class="h-4 w-4" />
        Update Password
    </button>
</form>
@endsection
