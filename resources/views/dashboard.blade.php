@extends('layouts.site')

@section('title', 'My Courses — ArtiWeb')

@section('content')

<div class="border-b border-(--color-border) bg-(--color-card) dark:border-white/10 dark:bg-(--color-card-dark)">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="mt-1.5 text-sm text-(--color-text-secondary)">Track your purchased courses and continue learning.</p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Continue Learning — populated client-side from localStorage, hidden by default --}}
    <a href="#" id="continueLearningCard" data-continue-learning
       class="mb-6 hidden items-center justify-between gap-4 rounded-2xl border border-(--color-primary)/25 bg-(--color-primary)/8 px-5 py-4 transition-colors hover:bg-(--color-primary)/12 sm:flex">
        <div class="flex items-center gap-4 overflow-hidden">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-(--color-primary) text-white">
                <x-icon name="play" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <div class="text-xs font-medium uppercase tracking-wide text-(--color-primary)">Continue learning</div>
                <div class="truncate text-sm font-semibold" data-continue-learning-title>&nbsp;</div>
            </div>
        </div>
        <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-(--color-primary)" />
    </a>

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="surface rounded-2xl p-5">
            <div class="text-2xl font-bold text-(--color-primary)">{{ $purchases->total() }}</div>
            <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Total Purchases</div>
        </div>
        <div class="surface rounded-2xl p-5">
            <div class="text-2xl font-bold text-(--color-success)">{{ $purchases->getCollection()->where('status', 'paid')->count() }}</div>
            <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Active Courses</div>
        </div>
        <div class="surface rounded-2xl p-5">
            <div class="text-2xl font-bold text-(--color-accent)">{{ $purchases->getCollection()->where('status', 'pending')->count() }}</div>
            <div class="mt-1 text-xs font-medium text-(--color-text-secondary)">Pending Payment</div>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold">My Courses</h2>
        <a href="{{ route('courses.index') }}" class="btn-secondary text-sm">
            <x-icon name="layout-grid" class="h-4 w-4" />
            Browse More
        </a>
    </div>

    @if($purchases->isEmpty())
        <div class="surface flex flex-col items-center rounded-2xl px-6 py-16 text-center">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="graduation-cap" class="h-7 w-7" />
            </span>
            <h3 class="text-base font-bold">No courses yet</h3>
            <p class="mt-1 text-sm text-(--color-text-secondary)">Start your learning journey today.</p>
            <a href="{{ route('courses.index') }}" class="btn-primary mt-5 text-sm">Browse Courses</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($purchases as $purchase)
            <div class="surface group flex flex-col overflow-hidden rounded-2xl transition-transform duration-200 hover:-translate-y-0.5">
                <div class="flex h-36 items-center justify-center overflow-hidden bg-(--color-primary)/8">
                    @if($purchase->course->cover_image_path)
                        <img src="{{ Storage::url($purchase->course->cover_image_path) }}" alt="{{ $purchase->course->title }}" class="h-full w-full object-cover">
                    @else
                        <x-icon name="graduation-cap" class="h-10 w-10 text-(--color-primary)/40" />
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <div class="mb-2 flex items-start justify-between gap-2">
                        <h3 class="text-sm font-bold leading-snug">{{ $purchase->course->title }}</h3>
                        @if($purchase->status === 'paid')
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-(--color-success)/10 px-2.5 py-1 text-[11px] font-semibold text-(--color-success)">
                                <x-icon name="check" class="h-3 w-3" />Active
                            </span>
                        @else
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-(--color-accent)/15 px-2.5 py-1 text-[11px] font-semibold text-(--color-accent)">
                                <x-icon name="clock" class="h-3 w-3" />Pending
                            </span>
                        @endif
                    </div>
                    <div class="mb-4 text-xs text-(--color-text-secondary)">
                        {{ $purchase->course->category->name }} · {{ $purchase->purchased_at->format('d M Y') }}
                    </div>

                    <div class="mt-auto">
                        @if($purchase->status === 'paid')
                            <a href="{{ route('courses.learn', $purchase->course->slug) }}" class="btn-primary w-full text-sm">
                                <x-icon name="play" class="h-4 w-4" />
                                Start Learning
                            </a>
                        @else
                            <div class="flex items-center gap-2 rounded-lg bg-(--color-accent)/10 px-3 py-2 text-xs text-(--color-accent)">
                                <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" />
                                Waiting for admin payment confirmation.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $purchases->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    try {
        var raw = localStorage.getItem('continue-learning');
        if (!raw) return;
        var data = JSON.parse(raw);
        if (!data || !data.url || !data.title) return;
        var card = document.getElementById('continueLearningCard');
        card.href = data.url;
        card.querySelector('[data-continue-learning-title]').textContent = data.title;
        card.classList.remove('hidden');
    } catch (e) { /* localStorage unavailable — skip silently */ }
})();
</script>
@endpush
@endsection
