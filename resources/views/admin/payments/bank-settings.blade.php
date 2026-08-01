@extends('admin.layout')
@section('title', 'Bank Information')

@section('content')

<div class="mx-auto max-w-2xl">
    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Back to Payments
    </a>

    <div class="lesson-card mt-4 p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="landmark" class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-base font-bold text-(--color-text) dark:text-white">Bank Information</h2>
                <p class="text-sm text-(--color-text-secondary)">Shown on every customer's Checkout page.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.payments.bank-settings.update') }}" class="mt-6 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="bank_name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Bank Name <span class="text-(--color-danger)">*</span></label>
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" required
                           class="input-field @error('bank_name') border-(--color-danger)! @enderror">
                    @error('bank_name')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="account_holder" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Account Holder <span class="text-(--color-danger)">*</span></label>
                    <input type="text" id="account_holder" name="account_holder" value="{{ old('account_holder', $setting->account_holder) }}" required
                           class="input-field @error('account_holder') border-(--color-danger)! @enderror">
                    @error('account_holder')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="account_number" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Account Number <span class="text-(--color-danger)">*</span></label>
                <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $setting->account_number) }}" required
                       class="input-field font-mono text-sm @error('account_number') border-(--color-danger)! @enderror">
                @error('account_number')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="iban" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">IBAN <span class="text-(--color-danger)">*</span></label>
                    <input type="text" id="iban" name="iban" value="{{ old('iban', $setting->iban) }}" required
                           class="input-field font-mono text-sm @error('iban') border-(--color-danger)! @enderror">
                    @error('iban')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="swift_bic" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">SWIFT / BIC <span class="text-(--color-danger)">*</span></label>
                    <input type="text" id="swift_bic" name="swift_bic" value="{{ old('swift_bic', $setting->swift_bic) }}" required
                           class="input-field font-mono text-sm @error('swift_bic') border-(--color-danger)! @enderror">
                    @error('swift_bic')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="payment_instructions" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                    Payment Instructions <span class="font-normal text-(--color-text-secondary)">(optional)</span>
                </label>
                <textarea id="payment_instructions" name="payment_instructions" rows="3"
                          placeholder="e.g. Please include your full name as the transfer reference."
                          class="input-field resize-none @error('payment_instructions') border-(--color-danger)! @enderror">{{ old('payment_instructions', $setting->payment_instructions) }}</textarea>
                @error('payment_instructions')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="whatsapp" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                        WhatsApp <span class="font-normal text-(--color-text-secondary)">(optional)</span>
                    </label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $setting->whatsapp) }}" placeholder="+212 6XX XXX XXX"
                           class="input-field @error('whatsapp') border-(--color-danger)! @enderror">
                    @error('whatsapp')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="support_email" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                        Support Email <span class="font-normal text-(--color-text-secondary)">(optional)</span>
                    </label>
                    <input type="email" id="support_email" name="support_email" value="{{ old('support_email', $setting->support_email) }}" placeholder="support@artiweb.ma"
                           class="input-field @error('support_email') border-(--color-danger)! @enderror">
                    @error('support_email')<p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-2 flex gap-3">
                <button type="submit" class="btn-primary flex-1 sm:flex-none">
                    <x-icon name="check-circle" class="h-4 w-4" />
                    Save Bank Information
                </button>
                <a href="{{ route('admin.payments.index') }}" class="btn-secondary flex-1 sm:flex-none">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
