@extends('layouts.app')

@section('title', __('auth.register_heading') . ' — ArtiWeb')
@section('heading', __('auth.register_heading'))
@section('subtitle', __('auth.register_subtitle'))

@section('content')
<form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
    @csrf

    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('auth.full_name_label') }}</label>
        <div class="relative">
            <x-icon name="user" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('auth.full_name_placeholder') }}" required autofocus
                   class="input-field pl-9 @error('name') border-(--color-danger)! @enderror">
        </div>
        @error('name')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('auth.email_label') }}</label>
        <div class="relative">
            <x-icon name="mail" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" required
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
            <input type="password" id="password" name="password" placeholder="{{ __('auth.password_hint_create') }}" required
                   class="input-field pl-9 @error('password') border-(--color-danger)! @enderror">
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('auth.confirm_password_label') }}</label>
        <div class="relative">
            <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('auth.confirm_password_placeholder') }}" required
                   class="input-field pl-9">
        </div>
    </div>

    <button type="submit" class="btn-primary mt-2 w-full">
        {{ __('auth.create_account') }}
        <x-icon name="arrow-right" class="h-4 w-4" />
    </button>
</form>

<div class="my-6 flex items-center gap-3 text-xs text-(--color-text-secondary)">
    <div class="h-px flex-1 bg-(--color-border) dark:bg-white/10"></div>
    <span>{{ __('auth.already_have_account') }}</span>
    <div class="h-px flex-1 bg-(--color-border) dark:bg-white/10"></div>
</div>

<a href="{{ route('login') }}" class="btn-secondary w-full">
    {{ __('auth.sign_in_instead') }}
</a>
@endsection
