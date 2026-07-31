{{-- Rendered once per payment regardless of how many times the row's action
     buttons appear (desktop table + mobile card both trigger the same
     dialog by id), so ids never collide. --}}
@foreach($payments as $payment)
    @if($payment->status === 'pending')
        <dialog id="reject-modal-{{ $payment->id }}" data-admin-modal class="w-full max-w-sm rounded-2xl border border-(--color-border) bg-(--color-card) p-6 shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-(--color-danger)/10 text-(--color-danger)">
                    <x-icon name="alert-triangle" class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="text-sm font-bold text-(--color-text) dark:text-white">Reject this payment?</h3>
                    <p class="mt-1 text-xs text-(--color-text-secondary)">
                        {{ $payment->user->name }}'s request for <strong>{{ $payment->course->title }}</strong> will be marked as rejected. They can resubmit later.
                    </p>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <button type="button" data-close-modal class="btn-secondary flex-1 !py-2 text-xs">Cancel</button>
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="flex-1">
                    @csrf @method('PUT')
                    <button class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-(--color-danger) px-3 py-2 text-xs font-semibold text-white transition-colors hover:opacity-90">
                        <x-icon name="x" class="h-3.5 w-3.5" />
                        Reject Payment
                    </button>
                </form>
            </div>
        </dialog>
    @elseif($payment->status === 'paid')
        <dialog id="cancel-modal-{{ $payment->id }}" data-admin-modal class="w-full max-w-sm rounded-2xl border border-(--color-border) bg-(--color-card) p-6 shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-(--color-danger)/10 text-(--color-danger)">
                    <x-icon name="ban" class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="text-sm font-bold text-(--color-text) dark:text-white">Cancel this approval?</h3>
                    <p class="mt-1 text-xs text-(--color-text-secondary)">
                        {{ $payment->user->name }} will immediately lose access to <strong>{{ $payment->course->title }}</strong>. The payment record is kept, not deleted.
                    </p>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <button type="button" data-close-modal class="btn-secondary flex-1 !py-2 text-xs">Keep Approved</button>
                <form method="POST" action="{{ route('admin.payments.cancel', $payment) }}" class="flex-1">
                    @csrf @method('PUT')
                    <button class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-(--color-danger) px-3 py-2 text-xs font-semibold text-white transition-colors hover:opacity-90">
                        <x-icon name="ban" class="h-3.5 w-3.5" />
                        Cancel Approval
                    </button>
                </form>
            </div>
        </dialog>
    @endif
@endforeach
