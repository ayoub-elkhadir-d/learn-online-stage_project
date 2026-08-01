@extends('admin.layout')
@section('title', 'Payment Details')

@php
    $isImage = $payment->receipt_path && !Str::endsWith(strtolower($payment->receipt_path), '.pdf');
@endphp

@section('content')

<a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
    <x-icon name="chevron-left" class="h-4 w-4" />
    Back to Payments
</a>

<div class="mt-4 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-(--color-text) dark:text-white">Payment #{{ $payment->id }}</h2>
        <p class="text-sm text-(--color-text-secondary)">Submitted {{ $payment->created_at->format('d M Y, H:i') }}</p>
    </div>
    <x-status-badge :status="$payment->status" class="!text-sm !px-3.5 !py-1.5" />
</div>

<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3 lg:items-start">

    <div class="flex flex-col gap-5 lg:col-span-2">

        {{-- User & Course --}}
        <div class="lesson-card grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">User</div>
                <div class="mt-2 flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-sm font-bold text-(--color-primary)">
                        {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-(--color-text) dark:text-white">{{ $payment->user->name }}</div>
                        <div class="truncate text-xs text-(--color-text-secondary)">{{ $payment->user->email }}</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">Course</div>
                <div class="mt-2 flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-(--color-text) dark:text-white">{{ $payment->course->title }}</div>
                        <div class="truncate text-xs text-(--color-text-secondary)">{{ $payment->course->category->name }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Amount & Transfer Date --}}
        <div class="lesson-card grid grid-cols-2 gap-5 p-5 sm:p-6">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">Amount</div>
                <div class="mt-1.5 text-xl font-extrabold text-(--color-primary)">{{ number_format($payment->course->price_mad, 0, ',', ' ') }} MAD</div>
            </div>
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">Transfer Date</div>
                <div class="mt-1.5 text-sm font-semibold text-(--color-text) dark:text-white">
                    {{ $payment->purchased_at ? $payment->purchased_at->format('d M Y, H:i') : 'N/A' }}
                </div>
            </div>
        </div>

        {{-- Bank information submitted by the user --}}
        <div class="lesson-card p-5 sm:p-6">
            <h3 class="flex items-center gap-2 text-sm font-bold text-(--color-text) dark:text-white">
                <x-icon name="landmark" class="h-4 w-4 text-(--color-primary)" />
                Bank Information
            </h3>
            <div class="mt-4 flex flex-col divide-y divide-(--color-border) text-sm dark:divide-white/10">
                <div class="flex items-center justify-between gap-3 py-2.5">
                    <span class="text-(--color-text-secondary)">Sender Account Name</span>
                    <span class="font-semibold text-(--color-text) dark:text-white">{{ $payment->full_name ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 py-2.5">
                    <span class="text-(--color-text-secondary)">Sender Account Number</span>
                    <span class="font-mono text-xs font-semibold text-(--color-text) dark:text-white">{{ $payment->rib ?? '—' }}</span>
                </div>
                @if($payment->reference)
                    <div class="flex flex-col gap-1 py-2.5">
                        <span class="text-(--color-text-secondary)">Additional Details</span>
                        <span class="text-sm text-(--color-text) dark:text-white">{{ $payment->reference }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-5">

        {{-- Receipt --}}
        <div class="lesson-card p-5 sm:p-6">
            <h3 class="flex items-center gap-2 text-sm font-bold text-(--color-text) dark:text-white">
                <x-icon name="image" class="h-4 w-4 text-(--color-primary)" />
                Receipt
            </h3>
            @if($payment->receipt_path)
                <button type="button" data-receipt-trigger
                        data-receipt-url="{{ Storage::url($payment->receipt_path) }}"
                        data-receipt-type="{{ $isImage ? 'image' : 'pdf' }}"
                        class="mt-3 block w-full overflow-hidden rounded-xl border border-(--color-border) transition-colors hover:border-(--color-primary)/40 dark:border-white/10">
                    @if($isImage)
                        <img src="{{ Storage::url($payment->receipt_path) }}" alt="Payment receipt" class="h-48 w-full object-cover">
                    @else
                        <div class="flex h-48 w-full flex-col items-center justify-center gap-2 bg-(--color-primary)/5 text-(--color-primary)">
                            <x-icon name="file-text" class="h-10 w-10" />
                            <span class="text-xs font-semibold">Open PDF receipt</span>
                        </div>
                    @endif
                </button>
                <button type="button" data-receipt-trigger
                        data-receipt-url="{{ Storage::url($payment->receipt_path) }}"
                        data-receipt-type="{{ $isImage ? 'image' : 'pdf' }}"
                        class="btn-secondary mt-3 w-full !py-2 text-xs">
                    <x-icon name="maximize-2" class="h-3.5 w-3.5" />
                    View Full Size
                </button>
            @else
                <p class="mt-3 text-sm text-(--color-text-secondary)">No receipt uploaded.</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="lesson-card p-5 sm:p-6">
            <h3 class="text-sm font-bold text-(--color-text) dark:text-white">Actions</h3>
            <div class="mt-4 flex flex-col gap-2">
                @if($payment->status === 'pending')
                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                        @csrf @method('PUT')
                        <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-(--color-success) px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:opacity-90">
                            <x-icon name="check" class="h-4 w-4" />
                            Approve
                        </button>
                    </form>
                    <button type="button" data-open-modal="reject-modal-{{ $payment->id }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-(--color-danger)/30 px-4 py-2.5 text-sm font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                        <x-icon name="x" class="h-4 w-4" />
                        Reject
                    </button>
                @elseif($payment->status === 'paid')
                    <button type="button" data-open-modal="cancel-modal-{{ $payment->id }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-(--color-danger)/30 px-4 py-2.5 text-sm font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                        <x-icon name="ban" class="h-4 w-4" />
                        Cancel Approval
                    </button>
                @else
                    <p class="text-center text-xs text-(--color-text-secondary)">No further action available for a {{ $payment->status }} payment.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@php $payments = collect([$payment]); @endphp
@include('admin.payments.partials.dialogs')

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
