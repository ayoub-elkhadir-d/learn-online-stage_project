@extends('admin.layout')
@section('title', __('admin.dashboard_title'))

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
    <div class="lesson-card p-5">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="book-open" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <div class="text-2xl font-extrabold text-(--color-text) dark:text-white">{{ $stats['courses'] }}</div>
                <div class="truncate text-xs font-medium text-(--color-text-secondary)">{{ __('admin.stat_courses') }}</div>
            </div>
        </div>
    </div>
    <div class="lesson-card p-5">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-(--color-accent)/10 text-(--color-accent)">
                <x-icon name="users" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <div class="text-2xl font-extrabold text-(--color-text) dark:text-white">{{ $stats['users'] }}</div>
                <div class="truncate text-xs font-medium text-(--color-text-secondary)">{{ __('admin.stat_users') }}</div>
            </div>
        </div>
    </div>
    <div class="lesson-card p-5">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-(--color-warning)/10 text-(--color-warning)">
                <x-icon name="clock" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <div class="text-2xl font-extrabold text-(--color-text) dark:text-white">{{ $stats['pending_payments'] }}</div>
                <div class="truncate text-xs font-medium text-(--color-text-secondary)">{{ __('admin.stat_pending_payments') }}</div>
            </div>
        </div>
    </div>
    <div class="lesson-card p-5">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-(--color-success)/10 text-(--color-success)">
                <x-icon name="dollar-sign" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <div class="text-2xl font-extrabold text-(--color-text) dark:text-white">{{ number_format($stats['revenue'], 0, ',', ' ') }}</div>
                <div class="truncate text-xs font-medium text-(--color-text-secondary)">{{ __('admin.stat_revenue') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-6 flex flex-wrap gap-2.5">
    <a href="{{ route('admin.courses.create') }}" class="btn-primary">
        <x-icon name="book-open" class="h-4 w-4" />
        {{ __('admin.quick_add_course') }}
    </a>
    <a href="{{ route('admin.categories.create') }}" class="btn-secondary">
        <x-icon name="tag" class="h-4 w-4" />
        {{ __('admin.quick_add_category') }}
    </a>
    <a href="{{ route('admin.payments.index') }}" class="btn-secondary">
        <x-icon name="credit-card" class="h-4 w-4" />
        {{ __('admin.quick_view_payments') }}
    </a>
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
        <x-icon name="users" class="h-4 w-4" />
        {{ __('admin.quick_view_users') }}
    </a>
</div>

<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">

    {{-- Recent payments --}}
    <div class="lesson-card p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-(--color-text) dark:text-white">{{ __('admin.recent_payments') }}</h2>
            <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-(--color-primary) hover:text-(--color-primary-dark)">{{ __('admin.view_all') }}</a>
        </div>
        <div class="mt-4 flex flex-col divide-y divide-(--color-border) dark:divide-white/10">
            @forelse($recentPayments as $payment)
                <a href="{{ route('admin.payments.show', $payment) }}" class="flex items-center gap-3 py-3 first:pt-0 last:pb-0 transition-colors hover:opacity-80">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">
                        {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $payment->user->name }}</div>
                        <div class="truncate text-xs text-(--color-text-secondary)">{{ $payment->course->title }}</div>
                    </div>
                    <x-status-badge :status="$payment->status" class="shrink-0" />
                </a>
            @empty
                <p class="py-6 text-center text-sm text-(--color-text-secondary)">{{ __('admin.no_payments_yet') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Recent enrollments --}}
    <div class="lesson-card p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-(--color-text) dark:text-white">{{ __('admin.recent_enrollments') }}</h2>
            <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-(--color-primary) hover:text-(--color-primary-dark)">{{ __('admin.view_all') }}</a>
        </div>
        <div class="mt-4 flex flex-col divide-y divide-(--color-border) dark:divide-white/10">
            @forelse($recentEnrollments as $enrollment)
                <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-success)/10 text-(--color-success)">
                        <x-icon name="check" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $enrollment->user->name }}</div>
                        <div class="truncate text-xs text-(--color-text-secondary)">{{ $enrollment->course->title }}</div>
                    </div>
                    <div class="shrink-0 text-xs text-(--color-text-secondary)">{{ $enrollment->purchased_at?->format('d M') }}</div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-(--color-text-secondary)">{{ __('admin.no_enrollments_yet') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Latest courses --}}
    <div class="lesson-card p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-(--color-text) dark:text-white">{{ __('admin.latest_courses') }}</h2>
            <a href="{{ route('admin.courses.index') }}" class="text-xs font-semibold text-(--color-primary) hover:text-(--color-primary-dark)">{{ __('admin.view_all') }}</a>
        </div>
        <div class="mt-4 flex flex-col divide-y divide-(--color-border) dark:divide-white/10">
            @forelse($latestCourses as $course)
                <a href="{{ route('admin.courses.edit', $course) }}" class="flex items-center gap-3 py-3 first:pt-0 last:pb-0 transition-colors hover:opacity-80">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                        <x-icon name="book-open" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</div>
                        <div class="truncate text-xs text-(--color-text-secondary)">{{ $course->category->name }}</div>
                    </div>
                    <div class="shrink-0 text-xs font-semibold text-(--color-primary)">{{ $course->price_mad }} {{ __('common.mad') }}</div>
                </a>
            @empty
                <p class="py-6 text-center text-sm text-(--color-text-secondary)">{{ __('admin.no_courses_yet') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Latest users --}}
    <div class="lesson-card p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-(--color-text) dark:text-white">{{ __('admin.latest_users') }}</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-(--color-primary) hover:text-(--color-primary-dark)">{{ __('admin.view_all') }}</a>
        </div>
        <div class="mt-4 flex flex-col divide-y divide-(--color-border) dark:divide-white/10">
            @forelse($latestUsers as $user)
                <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-accent)/10 text-xs font-bold text-(--color-accent)">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $user->name }}</div>
                        <div class="truncate text-xs text-(--color-text-secondary)">{{ $user->email }}</div>
                    </div>
                    <div class="shrink-0 text-xs text-(--color-text-secondary)">{{ $user->created_at->format('d M') }}</div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-(--color-text-secondary)">{{ __('admin.no_users_yet') }}</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
