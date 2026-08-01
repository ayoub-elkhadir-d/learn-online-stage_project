@extends('layouts.site')

@section('title', __('checkout.title') . ' — ' . $course->title . ' — ArtiWeb')

@php
    // $bankInfo is the single admin-managed PaymentSetting row, passed in by
    // CourseController@checkout — never influenced by anything in this
    // request. Fields with a copy button per the spec; Bank Name / Account
    // Holder are display-only.
    $bankFields = [
        ['label' => __('checkout.bank_name'), 'value' => $bankInfo->bank_name, 'icon' => 'landmark', 'copy' => false],
        ['label' => __('checkout.account_holder'), 'value' => $bankInfo->account_holder, 'icon' => 'user', 'copy' => false],
        ['label' => __('checkout.account_number'), 'value' => $bankInfo->account_number, 'icon' => 'credit-card', 'copy' => true],
        ['label' => __('checkout.iban'), 'value' => $bankInfo->iban, 'icon' => 'file-text', 'copy' => true],
        ['label' => __('checkout.swift'), 'value' => $bankInfo->swift_bic, 'icon' => 'link', 'copy' => true],
    ];
@endphp

@section('content')

<div class="mx-auto max-w-3xl px-4 pt-6 sm:px-6 lg:px-8">
    <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        {{ __('checkout.back_to_course') }}
    </a>
</div>

<div class="mx-auto max-w-3xl px-4 py-6 pb-16 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-(--color-text) dark:text-white sm:text-3xl">{{ __('checkout.title') }}</h1>
    <p class="mt-1.5 text-sm text-(--color-text-secondary)">{!! __('checkout.subtitle', ['course' => '<strong class="text-(--color-text) dark:text-white">' . e($course->title) . '</strong>']) !!}</p>

    @if($purchase && $purchase->status === 'pending')

        {{-- Already submitted — nothing left to do but wait for admin confirmation --}}
        <div class="lesson-card mt-8 flex flex-col items-center p-6 text-center sm:p-8">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-(--color-accent)/10 text-(--color-accent)">
                <x-icon name="clock" class="h-6 w-6" />
            </span>
            <h2 class="text-lg font-bold text-(--color-text) dark:text-white">{{ __('checkout.already_submitted_title') }}</h2>
            <p class="mt-1.5 max-w-sm text-sm text-(--color-text-secondary)">
                {{ __('checkout.already_submitted_text') }}
            </p>

            <div class="mt-5 w-full rounded-xl border border-(--color-border) p-4 text-left text-sm dark:border-white/10">
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-(--color-text-secondary)">{{ __('checkout.sender_name') }}</span>
                    <span class="font-semibold text-(--color-text) dark:text-white">{{ $purchase->full_name }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-(--color-text-secondary)">{{ __('checkout.sender_account') }}</span>
                    <span class="font-semibold text-(--color-text) dark:text-white">{{ $purchase->rib }}</span>
                </div>
                @if($purchase->reference)
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-(--color-text-secondary)">{{ __('checkout.details') }}</span>
                        <span class="max-w-[60%] text-right font-semibold text-(--color-text) dark:text-white">{{ $purchase->reference }}</span>
                    </div>
                @endif
                @if($purchase->receipt_path)
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-(--color-text-secondary)">{{ __('checkout.receipt') }}</span>
                        <a href="{{ Storage::url($purchase->receipt_path) }}" target="_blank" class="font-semibold text-(--color-primary) hover:text-(--color-primary-dark)">{{ __('common.view') }}</a>
                    </div>
                @endif
            </div>

            <a href="{{ route('courses.show', $course->slug) }}" class="btn-secondary mt-6 w-full sm:w-auto">
                <x-icon name="chevron-left" class="h-4 w-4" />
                {{ __('checkout.back_to_course_btn') }}
            </a>
        </div>

    @else

        @if(session('status'))
            <div class="mt-6 flex items-center gap-2 rounded-xl border border-(--color-primary)/20 bg-(--color-primary)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)" role="status">
                <x-icon name="alert-circle" class="h-4 w-4 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-6 rounded-xl border border-(--color-danger)/20 bg-(--color-danger)/10 px-4 py-3 text-sm text-(--color-danger)">
                <div class="flex items-center gap-2 font-semibold">
                    <x-icon name="alert-triangle" class="h-4 w-4 shrink-0" />
                    {{ __('common.please_fix_following') }}
                </div>
                <ul class="mt-1.5 list-disc pl-6 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ============ STEP 1 — Transfer information ============ --}}
        <div class="lesson-card mt-8 p-6 sm:p-8">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-sm font-bold text-white">1</span>
                <div>
                    <h2 class="text-base font-bold text-(--color-text) dark:text-white sm:text-lg">{{ __('checkout.step1_title') }}</h2>
                    <p class="text-xs text-(--color-text-secondary) sm:text-sm">{{ __('checkout.step1_subtitle') }}</p>
                </div>
            </div>

            @if(blank($bankInfo->bank_name))
                <div class="mt-5 flex items-center gap-2 rounded-xl border border-(--color-accent)/20 bg-(--color-accent)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)">
                    <x-icon name="alert-circle" class="h-4 w-4 shrink-0" />
                    {{ __('checkout.not_configured') }}
                </div>
            @else
                <div class="mt-6 flex flex-col">
                    @foreach($bankFields as $field)
                        <div class="flex items-center gap-3 py-3.5 {{ !$loop->first ? 'border-t border-(--color-border) dark:border-white/10' : '' }}">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                                <x-icon name="{{ $field['icon'] }}" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">{{ $field['label'] }}</div>
                                <div class="truncate text-sm font-bold text-(--color-text) dark:text-white">{{ $field['value'] }}</div>
                            </div>
                            @if($field['copy'])
                                <button type="button" data-copy-btn data-copy-text="{{ $field['value'] }}"
                                        class="flex shrink-0 items-center gap-1.5 rounded-lg border border-(--color-border) px-3 py-1.5 text-xs font-semibold text-(--color-text-secondary) transition-colors hover:border-(--color-primary)/40 hover:bg-(--color-primary)/5 hover:text-(--color-primary) dark:border-white/10">
                                    <x-icon name="copy" class="h-3.5 w-3.5" data-copy-icon />
                                    <span data-copy-label>{{ __('checkout.copy') }}</span>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($bankInfo->payment_instructions)
                    <p class="mt-4 rounded-xl bg-(--color-text)/5 p-3.5 text-xs leading-relaxed text-(--color-text-secondary) dark:bg-white/5">
                        {{ $bankInfo->payment_instructions }}
                    </p>
                @endif

                @if($bankInfo->whatsapp || $bankInfo->support_email)
                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-(--color-border) pt-4 dark:border-white/10">
                        <span class="text-xs font-semibold text-(--color-text-secondary)">{{ __('checkout.need_help') }}</span>
                        @if($bankInfo->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $bankInfo->whatsapp) }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-(--color-primary)/10 px-2.5 py-1.5 text-xs font-semibold text-(--color-primary) hover:bg-(--color-primary)/20">
                                <x-icon name="mail" class="h-3.5 w-3.5" />
                                {{ __('checkout.whatsapp') }}
                            </a>
                        @endif
                        @if($bankInfo->support_email)
                            <a href="mailto:{{ $bankInfo->support_email }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-(--color-primary)/10 px-2.5 py-1.5 text-xs font-semibold text-(--color-primary) hover:bg-(--color-primary)/20">
                                <x-icon name="mail" class="h-3.5 w-3.5" />
                                {{ $bankInfo->support_email }}
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        {{-- Payment notice --}}
        <div class="mt-5 flex items-start gap-3 rounded-2xl border border-(--color-primary)/20 bg-(--color-primary)/8 p-5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/15 text-(--color-primary)">
                <x-icon name="alert-circle" class="h-4 w-4" />
            </span>
            <p class="text-sm leading-relaxed text-(--color-primary-dark) dark:text-(--color-accent)">
                {{ __('checkout.payment_notice') }}
            </p>
        </div>

        <div class="mx-auto my-6 h-10 w-px bg-gradient-to-b from-(--color-primary)/40 to-transparent"></div>

        <form method="POST" action="{{ route('courses.purchase', $course->slug) }}" enctype="multipart/form-data" id="checkoutForm">
            @csrf
            <input type="hidden" name="payment_method" value="bank_transfer">

            {{-- ============ STEP 2 — Your transfer details ============ --}}
            <div class="lesson-card p-6 sm:p-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-sm font-bold text-white">2</span>
                    <div>
                        <h2 class="text-base font-bold text-(--color-text) dark:text-white sm:text-lg">{{ __('checkout.step2_title') }}</h2>
                        <p class="text-xs text-(--color-text-secondary) sm:text-sm">{{ __('checkout.step2_subtitle') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-4">
                    <div>
                        <label for="full_name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('checkout.sender_name') }}</label>
                        <div class="relative">
                            <x-icon name="user" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', auth()->user()->name) }}"
                                   placeholder="{{ __('checkout.sender_name_placeholder') }}" required
                                   class="input-field pl-9 @error('full_name') border-(--color-danger)! @enderror">
                        </div>
                        <p class="mt-1.5 text-xs text-(--color-text-secondary)">{{ __('checkout.sender_name_hint') }}</p>
                        @error('full_name')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rib" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('checkout.sender_account') }}</label>
                        <div class="relative">
                            <x-icon name="landmark" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                            <input type="text" id="rib" name="rib" value="{{ old('rib') }}"
                                   placeholder="{{ __('checkout.sender_account_placeholder') }}" required
                                   class="input-field pl-9 @error('rib') border-(--color-danger)! @enderror">
                        </div>
                        <p class="mt-1.5 text-xs text-(--color-text-secondary)">{{ __('checkout.sender_account_hint') }}</p>
                        @error('rib')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reference" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">
                            {{ __('checkout.additional_details') }} <span class="font-normal text-(--color-text-secondary)">({{ __('common.optional') }})</span>
                        </label>
                        <textarea id="reference" name="reference" rows="3"
                                  placeholder="{{ __('checkout.additional_details_placeholder') }}"
                                  class="input-field resize-none @error('reference') border-(--color-danger)! @enderror">{{ old('reference') }}</textarea>
                        <p class="mt-1.5 text-xs text-(--color-text-secondary)">{{ __('checkout.additional_details_hint') }}</p>
                        @error('reference')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">{{ __('checkout.payment_receipt') }}</label>

                        <div id="receiptArea" data-receipt-area
                             class="relative flex min-h-40 cursor-pointer flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-(--color-border) px-6 py-8 text-center transition-colors hover:border-(--color-primary)/50 hover:bg-(--color-primary)/5 dark:border-white/15">

                            <div data-receipt-placeholder class="flex flex-col items-center gap-1.5">
                                <x-icon name="upload-cloud" class="h-8 w-8 text-(--color-text-secondary)" />
                                <div class="text-sm font-medium text-(--color-text) dark:text-white">{{ __('checkout.receipt_upload_hint') }}</div>
                                <div class="text-xs text-(--color-text-secondary)">{{ __('checkout.receipt_formats') }}</div>
                            </div>

                            <div data-receipt-preview class="hidden w-full flex-col items-center gap-3">
                                <img data-receipt-thumb class="hidden h-28 w-28 rounded-lg object-cover shadow-soft" alt="{{ __('checkout.payment_receipt') }}">
                                <div data-receipt-file-icon class="hidden h-16 w-16 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                                    <x-icon name="file-text" class="h-7 w-7" />
                                </div>
                                <div class="min-w-0 text-center">
                                    <div data-receipt-filename class="max-w-[16rem] truncate text-sm font-semibold text-(--color-text) dark:text-white"></div>
                                    <div data-receipt-filesize class="text-xs text-(--color-text-secondary)"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" data-receipt-replace class="btn-secondary !px-3 !py-1.5 text-xs">
                                        <x-icon name="upload-cloud" class="h-3.5 w-3.5" />
                                        {{ __('common.replace') }}
                                    </button>
                                    <button type="button" data-receipt-remove class="inline-flex items-center gap-1.5 rounded-xl border border-(--color-danger)/30 px-3 py-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                        <x-icon name="x" class="h-3.5 w-3.5" />
                                        {{ __('common.remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input type="file" name="receipt" id="receiptInput" class="hidden" accept="image/png,image/jpeg,image/jpg,application/pdf" required>
                        @error('receipt')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mx-auto my-6 h-10 w-px bg-gradient-to-b from-(--color-primary)/40 to-transparent"></div>

            {{-- ============ STEP 3 — Confirmation ============ --}}
            <div class="lesson-card p-6 sm:p-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-sm font-bold text-white">3</span>
                    <div>
                        <h2 class="text-base font-bold text-(--color-text) dark:text-white sm:text-lg">{{ __('checkout.step3_title') }}</h2>
                        <p class="text-xs text-(--color-text-secondary) sm:text-sm">{{ __('checkout.step3_subtitle') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col text-sm">
                    <div class="flex items-center justify-between gap-3 py-2.5">
                        <span class="text-(--color-text-secondary)">{{ __('checkout.course') }}</span>
                        <span class="max-w-[60%] truncate text-right font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-(--color-border) py-2.5 dark:border-white/10">
                        <span class="text-(--color-text-secondary)">{{ __('checkout.price') }}</span>
                        <span class="font-semibold text-(--color-text) dark:text-white">{{ number_format($course->price_mad, 0, ',', ' ') }} {{ __('common.mad') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-(--color-border) py-2.5 dark:border-white/10">
                        <span class="text-(--color-text-secondary)">{{ __('checkout.uploaded_receipt') }}</span>
                        <span data-confirm-receipt class="max-w-[60%] truncate text-right font-semibold text-(--color-text-secondary)">{{ __('checkout.not_uploaded_yet') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-(--color-border) py-2.5 dark:border-white/10">
                        <span class="text-(--color-text-secondary)">{{ __('checkout.transfer_amount') }}</span>
                        <span class="text-lg font-extrabold text-(--color-primary)">{{ number_format($course->price_mad, 0, ',', ' ') }} {{ __('common.mad') }}</span>
                    </div>
                </div>

                <label class="mt-6 flex items-start gap-3 rounded-xl border border-(--color-border) p-4 dark:border-white/10">
                    <input type="checkbox" id="confirmCheckbox" required
                           class="mt-0.5 h-4 w-4 shrink-0 rounded border-(--color-border) accent-[var(--color-primary)]">
                    <span class="text-sm text-(--color-text-secondary)">{{ __('checkout.confirm_checkbox') }}</span>
                </label>

                <button type="submit" id="submitBtn" disabled class="btn-primary mt-5 w-full text-base opacity-50">
                    <x-icon name="lock" class="h-4 w-4" />
                    <span data-submit-label>{{ __('checkout.submit_payment') }}</span>
                </button>

                <p class="mt-4 flex items-center justify-center gap-1.5 text-center text-xs text-(--color-text-secondary)">
                    <x-icon name="shield" class="h-3.5 w-3.5 text-(--color-primary)" />
                    {{ __('checkout.secure_note') }}
                </p>
            </div>
        </form>
    @endif
</div>

{{-- Copy-to-clipboard toast --}}
<div id="copyToast" class="pointer-events-none fixed inset-x-0 bottom-6 z-50 flex translate-y-2 justify-center opacity-0 transition-all duration-200">
    <span class="flex items-center gap-2 rounded-full bg-(--color-text) px-4 py-2.5 text-sm font-medium text-white shadow-lift dark:bg-white dark:text-(--color-text)">
        <x-icon name="check-circle" class="h-4 w-4 text-(--color-accent)" />
        {{ __('checkout.copied') }}
    </span>
</div>

@push('scripts')
<script>
window.__checkoutI18n = @json(__('checkout.js'));
(function () {
    var i18n = window.__checkoutI18n || {};

    // ---- Copy to clipboard ----
    var toast = document.getElementById('copyToast');
    var toastTimer;
    function showToast() {
        if (!toast) return;
        clearTimeout(toastTimer);
        toast.classList.remove('opacity-0', 'translate-y-2');
        toast.classList.add('opacity-100', 'translate-y-0');
        toastTimer = setTimeout(function () {
            toast.classList.add('opacity-0', 'translate-y-2');
            toast.classList.remove('opacity-100', 'translate-y-0');
        }, 1600);
    }

    document.querySelectorAll('[data-copy-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.dataset.copyText || '';
            var reset = function () {
                var label = btn.querySelector('[data-copy-label]');
                if (label) label.textContent = i18n.copy;
            };
            var onCopied = function () {
                var label = btn.querySelector('[data-copy-label]');
                if (label) label.textContent = i18n.copied;
                showToast();
                setTimeout(reset, 1600);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(onCopied).catch(function () {});
            } else {
                var helper = document.createElement('textarea');
                helper.value = text;
                helper.style.position = 'fixed';
                helper.style.opacity = '0';
                document.body.appendChild(helper);
                helper.select();
                try { document.execCommand('copy'); onCopied(); } catch (e) {}
                document.body.removeChild(helper);
            }
        });
    });

    // ---- Receipt upload: drag & drop, preview, replace, remove ----
    var receiptInput = document.getElementById('receiptInput');
    var area = document.querySelector('[data-receipt-area]');
    if (receiptInput && area) {
        var placeholder = area.querySelector('[data-receipt-placeholder]');
        var preview = area.querySelector('[data-receipt-preview]');
        var thumb = area.querySelector('[data-receipt-thumb]');
        var fileIcon = area.querySelector('[data-receipt-file-icon]');
        var filenameEl = area.querySelector('[data-receipt-filename]');
        var filesizeEl = area.querySelector('[data-receipt-filesize]');
        var confirmReceipt = document.querySelector('[data-confirm-receipt]');

        function formatSize(bytes) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        function renderFile(file) {
            if (!file) return;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
            preview.classList.add('flex');

            filenameEl.textContent = file.name;
            filesizeEl.textContent = formatSize(file.size);
            if (confirmReceipt) {
                confirmReceipt.textContent = file.name;
                confirmReceipt.classList.remove('text-(--color-text-secondary)');
                confirmReceipt.classList.add('text-(--color-primary)');
            }

            if (file.type === 'application/pdf') {
                thumb.classList.add('hidden');
                fileIcon.classList.remove('hidden');
                fileIcon.classList.add('flex');
            } else {
                fileIcon.classList.add('hidden');
                fileIcon.classList.remove('flex');
                var reader = new FileReader();
                reader.onload = function (e) {
                    thumb.src = e.target.result;
                    thumb.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function resetArea() {
            receiptInput.value = '';
            placeholder.classList.remove('hidden');
            preview.classList.add('hidden');
            preview.classList.remove('flex');
            thumb.classList.add('hidden');
            thumb.removeAttribute('src');
            fileIcon.classList.add('hidden');
            fileIcon.classList.remove('flex');
            if (confirmReceipt) {
                confirmReceipt.textContent = i18n.not_uploaded_yet;
                confirmReceipt.classList.add('text-(--color-text-secondary)');
                confirmReceipt.classList.remove('text-(--color-primary)');
            }
        }

        area.addEventListener('click', function (e) {
            if (e.target.closest('[data-receipt-replace]') || e.target.closest('[data-receipt-remove]')) return;
            receiptInput.click();
        });

        receiptInput.addEventListener('change', function () {
            if (this.files && this.files[0]) renderFile(this.files[0]);
        });

        var replaceBtn = area.querySelector('[data-receipt-replace]');
        if (replaceBtn) replaceBtn.addEventListener('click', function () { receiptInput.click(); });

        var removeBtn = area.querySelector('[data-receipt-remove]');
        if (removeBtn) removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            resetArea();
        });

        ['dragenter', 'dragover'].forEach(function (evt) {
            area.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                area.classList.add('border-(--color-primary)', 'bg-(--color-primary)/5');
            });
        });
        ['dragleave', 'drop'].forEach(function (evt) {
            area.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                area.classList.remove('border-(--color-primary)', 'bg-(--color-primary)/5');
            });
        });
        area.addEventListener('drop', function (e) {
            var files = e.dataTransfer.files;
            if (files && files[0]) {
                receiptInput.files = files;
                renderFile(files[0]);
            }
        });
    }

    // ---- Confirmation checkbox gates the submit button ----
    var checkbox = document.getElementById('confirmCheckbox');
    var submitBtn = document.getElementById('submitBtn');
    if (checkbox && submitBtn) {
        checkbox.addEventListener('change', function () {
            submitBtn.disabled = !this.checked;
            submitBtn.classList.toggle('opacity-50', !this.checked);
        });
    }

    // ---- Loading state on submit ----
    var form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function () {
            var label = submitBtn.querySelector('[data-submit-label]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'pointer-events-none');
            if (label) label.textContent = i18n.submitting;
        });
    }
})();
</script>
@endpush

@endsection
