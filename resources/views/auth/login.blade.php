@extends('layouts.app')

@section('title', __('auth.login_heading') . ' — ArtiWeb')
@section('heading', __('auth.login_heading'))
@section('subtitle', __('auth.login_subtitle'))

@section('content')
<form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
    @csrf

    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('auth.email_label') }}</label>
        <div class="relative">
            <x-icon name="mail" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" required autofocus
                   class="input-field pl-9 @error('email') border-(--color-danger)! @enderror">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('auth.password_label') }}</label>
        <div class="relative">
            <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="password" id="password" name="password" placeholder="{{ __('auth.password_placeholder') }}" required
                   class="input-field pl-9 @error('password') border-(--color-danger)! @enderror">
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-(--color-text-secondary)">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-(--color-border) accent-[var(--color-primary)]">
            {{ __('auth.remember_me') }}
        </label>
        <a href="{{ route('password.request') }}" class="font-medium text-(--color-primary) transition-colors hover:text-(--color-primary-dark)">
            {{ __('auth.forgot_password') }}
        </a>
    </div>

    <button type="submit" class="btn-primary mt-2 w-full">
        {{ __('auth.sign_in') }}
        <x-icon name="arrow-right" class="h-4 w-4" />
    </button>
</form>

<div class="my-6 flex items-center gap-3 text-xs text-(--color-text-secondary)">
    <div class="h-px flex-1 bg-(--color-border) dark:bg-white/10"></div>
    <span>{{ __('auth.new_to_artiweb') }}</span>
    <div class="h-px flex-1 bg-(--color-border) dark:bg-white/10"></div>
</div>

<a href="{{ route('register') }}" class="btn-secondary w-full">
    {{ __('auth.create_an_account') }}
</a>
@endsection
