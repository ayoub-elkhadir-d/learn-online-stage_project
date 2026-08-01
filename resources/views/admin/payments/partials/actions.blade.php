@if($payment->status === 'pending')
    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
        @csrf @method('PUT')
        <button class="inline-flex items-center gap-1.5 rounded-xl bg-(--color-success) px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:opacity-90">
            <x-icon name="check" class="h-3.5 w-3.5" />
            {{ __('admin.approve') }}
        </button>
    </form>
    <button type="button" data-open-modal="reject-modal-{{ $payment->id }}"
            class="inline-flex items-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
        <x-icon name="x" class="h-3.5 w-3.5" />
        {{ __('admin.reject') }}
    </button>
@elseif($payment->status === 'paid')
    <button type="button" data-open-modal="cancel-modal-{{ $payment->id }}"
            class="inline-flex items-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
        <x-icon name="ban" class="h-3.5 w-3.5" />
        {{ __('admin.cancel_approval') }}
    </button>
@endif
