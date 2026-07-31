@extends('admin.layout')
@section('title', 'Payments')

@php
    $statusFilters = [
        '' => 'All',
        'pending' => 'Pending',
        'paid' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-(--color-text) dark:text-white">Payment Requests</h2>
        <p class="text-sm text-(--color-text-secondary)">{{ $payments->total() }} {{ Str::plural('request', $payments->total()) }} total</p>
    </div>
</div>

<form method="GET" action="{{ route('admin.payments.index') }}" class="mb-5 flex flex-col gap-3">
    <div class="relative max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by user, email or course..."
               class="input-field pl-9">
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @foreach($statusFilters as $value => $label)
            <button type="submit" name="status" value="{{ $value }}"
                    class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-semibold transition-colors {{ request('status', '') === $value ? 'border-(--color-primary) bg-(--color-primary) text-white' : 'border-(--color-border) text-(--color-text-secondary) hover:border-(--color-primary)/40 hover:text-(--color-primary) dark:border-white/10' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</form>

@if($payments->isEmpty())
    <div class="lesson-card flex flex-col items-center px-6 py-16 text-center">
        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
            <x-icon name="credit-card" class="h-7 w-7" />
        </span>
        <h3 class="text-base font-bold text-(--color-text) dark:text-white">No payment requests found</h3>
        <p class="mt-1 max-w-sm text-sm text-(--color-text-secondary)">
            @if(request('q') || request('status')) Try a different search or filter. @else Payment requests from users will appear here. @endif
        </p>
    </div>
@else

    {{-- Desktop / tablet table --}}
    <div class="lesson-card hidden overflow-x-auto md:block">
        <table class="admin-table w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">User</th>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Course</th>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Amount</th>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Receipt</th>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Date</th>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Status</th>
                    <th class="sticky top-0 whitespace-nowrap bg-(--color-card) px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-(--color-text-secondary) dark:bg-(--color-card-dark)">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr class="border-t border-(--color-border) transition-colors hover:bg-black/[.02] dark:border-white/10 dark:hover:bg-white/[.03]">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">
                                    {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $payment->user->name }}</div>
                                    <div class="truncate text-xs text-(--color-text-secondary)">{{ $payment->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-[14rem] px-4 py-3.5">
                            <div class="truncate text-sm font-medium text-(--color-text) dark:text-white">{{ $payment->course->title }}</div>
                            <div class="truncate text-xs text-(--color-text-secondary)">{{ $payment->course->category->name }}</div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-sm font-semibold text-(--color-text) dark:text-white">
                            {{ number_format($payment->course->price_mad, 0, ',', ' ') }} MAD
                        </td>
                        <td class="px-4 py-3.5">
                            @if($payment->receipt_path)
                                <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-(--color-primary)/10 px-2.5 py-1 text-xs font-semibold text-(--color-primary) hover:bg-(--color-primary)/20">
                                    <x-icon name="image" class="h-3.5 w-3.5" />
                                    View
                                </a>
                            @else
                                <span class="text-xs text-(--color-text-secondary)">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-xs text-(--color-text-secondary)">
                            {{ $payment->purchased_at ? $payment->purchased_at->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5">
                            <x-status-badge :status="$payment->status" />
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.payments.show', $payment) }}" title="View details"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                                    <x-icon name="chevron-right" class="h-4 w-4" />
                                </a>
                                @include('admin.payments.partials.actions', ['payment' => $payment])
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile card list --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($payments as $payment)
            <div class="lesson-card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">
                            {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $payment->user->name }}</div>
                            <div class="truncate text-xs text-(--color-text-secondary)">{{ $payment->course->title }}</div>
                        </div>
                    </div>
                    <x-status-badge :status="$payment->status" class="shrink-0" />
                </div>

                <div class="mt-3 flex items-center justify-between text-xs text-(--color-text-secondary)">
                    <span>{{ $payment->purchased_at ? $payment->purchased_at->format('d M Y') : 'N/A' }}</span>
                    <span class="font-semibold text-(--color-text) dark:text-white">{{ number_format($payment->course->price_mad, 0, ',', ' ') }} MAD</span>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn-secondary !py-1.5 text-xs">
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        Details
                    </a>
                    @include('admin.payments.partials.actions', ['payment' => $payment])
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $payments->links() }}</div>

    @include('admin.payments.partials.dialogs')
@endif

@push('scripts')
<script>
document.querySelectorAll('[data-open-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var dialog = document.getElementById(btn.dataset.openModal);
        if (dialog) dialog.showModal();
    });
});
document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var dialog = btn.closest('dialog');
        if (dialog) dialog.close();
    });
});
</script>
@endpush

@endsection
