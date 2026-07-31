@extends('layouts.site')

@section('title', 'Account Settings — ArtiWeb')

@section('content')

<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">

    <div class="mb-8 flex items-center gap-4">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-lg font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </span>
        <div>
            <h1 class="text-xl font-extrabold tracking-tight text-(--color-text) dark:text-white">Account Settings</h1>
            <p class="mt-0.5 text-sm text-(--color-text-secondary)">Manage your profile information and password</p>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-6">
        @csrf
        @method('PUT')

        <div class="lesson-card p-6">
            <h2 class="text-sm font-bold text-(--color-text) dark:text-white">Profile Information</h2>
            <p class="mt-1 text-xs text-(--color-text-secondary)">Update your name and email address.</p>

            <div class="mt-5 flex flex-col gap-4">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Full Name</label>
                    <div class="relative">
                        <x-icon name="user" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required
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
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                               class="input-field pl-9 @error('email') border-(--color-danger)! @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="lesson-card p-6">
            <h2 class="text-sm font-bold text-(--color-text) dark:text-white">Change Password</h2>
            <p class="mt-1 text-xs text-(--color-text-secondary)">Leave blank to keep your current password.</p>

            <div class="mt-5 flex flex-col gap-4">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">New Password</label>
                    <div class="relative">
                        <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep current password"
                               class="input-field pl-9 @error('password') border-(--color-danger)! @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Confirm New Password</label>
                    <div class="relative">
                        <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password"
                               class="input-field pl-9">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse items-center justify-between gap-3 sm:flex-row">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
                <x-icon name="chevron-left" class="h-4 w-4" />
                Back to My Courses
            </a>
            <button type="submit" class="btn-primary w-full sm:w-auto">
                <x-icon name="check-circle" class="h-4 w-4" />
                Save Changes
            </button>
        </div>
    </form>

    <div class="mt-8 border-t border-(--color-border) pt-6 dark:border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-1.5 text-sm font-medium text-(--color-danger) transition-colors hover:opacity-80">
                <x-icon name="log-out" class="h-4 w-4" />
                Sign Out
            </button>
        </form>
    </div>
</div>

@endsection
