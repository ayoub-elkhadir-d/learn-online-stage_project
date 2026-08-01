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
                    <h3 class="text-sm font-bold text-(--color-text) dark:text-white">{{ __('admin.reject_confirm_title') }}</h3>
                    <p class="mt-1 text-xs text-(--color-text-secondary)">
                        {!! __('admin.reject_confirm_text', ['user' => e($payment->user->name), 'course' => '<strong>' . e($payment->course->title) . '</strong>']) !!}
                    </p>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <button type="button" data-close-modal class="btn-secondary flex-1 !py-2 text-xs">{{ __('common.cancel') }}</button>
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="flex-1">
                    @csrf @method('PUT')
                    <button class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-(--color-danger) px-3 py-2 text-xs font-semibold text-white transition-colors hover:opacity-90">
                        <x-icon name="x" class="h-3.5 w-3.5" />
                        {{ __('admin.reject_confirm_btn') }}
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
                    <h3 class="text-sm font-bold text-(--color-text) dark:text-white">{{ __('admin.cancel_confirm_title') }}</h3>
                    <p class="mt-1 text-xs text-(--color-text-secondary)">
                        {!! __('admin.cancel_confirm_text', ['user' => e($payment->user->name), 'course' => '<strong>' . e($payment->course->title) . '</strong>']) !!}
                    </p>
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <button type="button" data-close-modal class="btn-secondary flex-1 !py-2 text-xs">{{ __('admin.keep_approved') }}</button>
                <form method="POST" action="{{ route('admin.payments.cancel', $payment) }}" class="flex-1">
                    @csrf @method('PUT')
                    <button class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-(--color-danger) px-3 py-2 text-xs font-semibold text-white transition-colors hover:opacity-90">
                        <x-icon name="ban" class="h-3.5 w-3.5" />
                        {{ __('admin.cancel_confirm_btn') }}
                    </button>
                </form>
            </div>
        </dialog>
    @endif
@endforeach
