@extends('layouts.site')

@section('title', 'Checkout — ' . $course->title . ' — ArtiWeb')

@section('content')

<div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
    <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) dark:hover:text-white">
        <x-icon name="chevron-left" class="h-4 w-4" />
        Back to course
    </a>
</div>

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-(--color-text) dark:text-white sm:text-3xl">Secure Checkout</h1>
    <p class="mt-1.5 text-sm text-(--color-text-secondary)">Complete your payment to get instant access to <strong class="text-(--color-text) dark:text-white">{{ $course->title }}</strong></p>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[1.6fr_1fr] lg:items-start">

        {{-- Left: payment form --}}
        <div class="lesson-card p-5 sm:p-6">

            @if($purchase && $purchase->status === 'pending')
                <div class="flex flex-col items-center py-6 text-center">
                    <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-(--color-accent)/10 text-(--color-accent)">
                        <x-icon name="clock" class="h-6 w-6" />
                    </span>
                    <h2 class="text-lg font-bold text-(--color-text) dark:text-white">Payment Already Submitted</h2>
                    <p class="mt-1.5 max-w-sm text-sm text-(--color-text-secondary)">
                        You've already submitted a payment request for this course. It's awaiting admin confirmation.
                    </p>

                    <div class="mt-5 w-full rounded-xl border border-(--color-border) p-4 text-left text-sm dark:border-white/10">
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-(--color-text-secondary)">Full Name</span>
                            <span class="font-semibold text-(--color-text) dark:text-white">{{ $purchase->full_name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-(--color-text-secondary)">RIB</span>
                            <span class="font-semibold text-(--color-text) dark:text-white">{{ $purchase->rib }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-(--color-text-secondary)">Reference</span>
                            <span class="font-semibold text-(--color-text) dark:text-white">{{ $purchase->reference }}</span>
                        </div>
                        @if($purchase->receipt_path)
                            <div class="flex items-center justify-between py-1.5">
                                <span class="text-(--color-text-secondary)">Receipt</span>
                                <a href="{{ Storage::url($purchase->receipt_path) }}" target="_blank" class="font-semibold text-(--color-primary) hover:text-(--color-primary-dark)">View</a>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('courses.show', $course->slug) }}" class="btn-secondary mt-6">
                        <x-icon name="chevron-left" class="h-4 w-4" />
                        Back to Course
                    </a>
                </div>
            @else

                @if(session('status'))
                    <div class="mb-5 flex items-center gap-2 rounded-xl border border-(--color-primary)/20 bg-(--color-primary)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)" role="status">
                        <x-icon name="alert-circle" class="h-4 w-4 shrink-0" />
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-(--color-danger)/20 bg-(--color-danger)/10 px-4 py-3 text-sm text-(--color-danger)">
                        <div class="flex items-center gap-2 font-semibold">
                            <x-icon name="alert-triangle" class="h-4 w-4 shrink-0" />
                            Please fix the following
                        </div>
                        <ul class="mt-1.5 list-disc pl-6 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Step indicator --}}
                <div class="mb-6 flex items-center gap-2">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-xs font-bold text-white">1</span>
                    <span class="h-0.5 flex-1 max-w-16 bg-(--color-primary)"></span>
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-(--color-text)/8 text-xs font-bold text-(--color-text-secondary) dark:bg-white/10">2</span>
                    <span class="ml-3 flex flex-col">
                        <span class="text-xs font-semibold text-(--color-primary)">Payment Details</span>
                        <span class="text-[11px] text-(--color-text-secondary)">Then admin confirmation</span>
                    </span>
                </div>

                <h2 class="flex items-center gap-2 text-base font-bold text-(--color-text) dark:text-white">
                    <x-icon name="landmark" class="h-5 w-5 text-(--color-primary)" />
                    Bank Transfer Payment
                </h2>

                <form method="POST" action="{{ route('courses.purchase', $course->slug) }}" enctype="multipart/form-data" id="checkoutForm" class="mt-5 flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="payment_method" value="bank_transfer">

                    <div class="flex items-center gap-3 rounded-xl border border-(--color-border) p-3.5 dark:border-white/10">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-(--color-primary)/10">
                            @if($course->cover_image_path)
                                <img src="{{ Storage::url($course->cover_image_path) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                            @else
                                <x-icon name="graduation-cap" class="h-5 w-5 text-(--color-primary)" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</div>
                            <div class="text-xs text-(--color-text-secondary)">{{ $course->category->name }}</div>
                        </div>
                        <div class="shrink-0 text-right">
                            <div class="text-base font-extrabold text-(--color-primary)">{{ number_format($course->price_mad, 0, ',', ' ') }} MAD</div>
                            <div class="text-[10px] text-(--color-text-secondary)">One-time payment</div>
                        </div>
                    </div>

                    <div>
                        <label for="full_name" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Full Name</label>
                        <div class="relative">
                            <x-icon name="user" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', auth()->user()->name) }}"
                                   placeholder="Your full name as registered in the bank" required
                                   class="input-field pl-9 @error('full_name') border-(--color-danger)! @enderror">
                        </div>
                        <p class="mt-1.5 text-xs text-(--color-text-secondary)">Enter the full name used on your bank account.</p>
                        @error('full_name')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rib" class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Your RIB</label>
                        <div class="relative">
                            <x-icon name="landmark" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                            <input type="text" id="rib" name="rib" value="{{ old('rib') }}"
                                   placeholder="e.g. 123 456 789 0123456789 01" required
                                   class="input-field pl-9 @error('rib') border-(--color-danger)! @enderror">
                        </div>
                        <p class="mt-1.5 text-xs text-(--color-text-secondary)">The RIB of the account you're transferring from.</p>
                        @error('rib')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-(--color-text) dark:text-white/90">Receipt Screenshot</label>
                        <div id="receiptArea" data-receipt-area
                             class="flex cursor-pointer flex-col items-center gap-1.5 rounded-xl border-2 border-dashed border-(--color-border) px-6 py-8 text-center transition-colors hover:border-(--color-primary)/50 hover:bg-(--color-primary)/5 dark:border-white/15">
                            <x-icon name="upload-cloud" class="h-8 w-8 text-(--color-text-secondary)" data-receipt-icon />
                            <div data-receipt-placeholder>
                                <div class="text-sm font-medium text-(--color-text) dark:text-white">Click to upload receipt</div>
                                <div class="mt-0.5 text-xs text-(--color-text-secondary)">JPG, PNG or PDF — Max 5MB</div>
                            </div>
                            <div data-receipt-filename class="hidden text-sm font-medium text-(--color-primary)"></div>
                        </div>
                        <input type="file" name="receipt" id="receiptInput" class="hidden" accept="image/png,image/jpeg,image/jpg,application/pdf" required>
                        <p class="mt-1.5 text-xs text-(--color-text-secondary)">Upload a clear screenshot or PDF of the payment confirmation from your bank.</p>
                        @error('receipt')
                            <p class="mt-1.5 text-xs text-(--color-danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" id="submitBtn" class="btn-primary mt-2 w-full">
                        <x-icon name="lock" class="h-4 w-4" data-submit-icon />
                        <span data-submit-label>Submit Payment Proof — {{ number_format($course->price_mad, 0, ',', ' ') }} MAD</span>
                    </button>

                    <p class="flex items-center justify-center gap-1.5 text-center text-xs text-(--color-text-secondary)">
                        <x-icon name="shield" class="h-3.5 w-3.5 text-(--color-primary)" />
                        Your information is securely submitted. Admin will confirm your payment.
                    </p>
                </form>
            @endif
        </div>

        {{-- Right: order summary --}}
        <div class="lesson-card p-6 lg:sticky lg:top-24">
            <h2 class="flex items-center gap-2 text-sm font-bold text-(--color-text) dark:text-white">
                <x-icon name="file-text" class="h-4 w-4 text-(--color-primary)" />
                Order Summary
            </h2>

            <div class="mt-4 flex flex-col gap-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-(--color-text-secondary)">Course</span>
                    <span class="max-w-[60%] truncate text-right font-semibold text-(--color-text) dark:text-white">{{ $course->title }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-(--color-text-secondary)">Category</span>
                    <span class="text-(--color-text-secondary)">{{ $course->category->name }}</span>
                </div>
                <div class="my-2 border-t border-(--color-border) dark:border-white/10"></div>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-(--color-text) dark:text-white">Total</span>
                    <span class="text-xl font-extrabold text-(--color-primary)">{{ number_format($course->price_mad, 0, ',', ' ') }} MAD</span>
                </div>
            </div>

            <ul class="mt-6 flex flex-col text-sm">
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    One-time payment — lifetime access
                </li>
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="shield" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    Admin confirmation required
                </li>
                <li class="feature-row flex items-center gap-2.5 py-2.5 text-(--color-text-secondary)">
                    <x-icon name="lock" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                    Secure data handling
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var receiptInput = document.getElementById('receiptInput');
    var area = document.querySelector('[data-receipt-area]');
    if (receiptInput && area) {
        area.addEventListener('click', function () { receiptInput.click(); });
        receiptInput.addEventListener('change', function () {
            var placeholder = area.querySelector('[data-receipt-placeholder]');
            var filename = area.querySelector('[data-receipt-filename]');
            if (this.files && this.files[0]) {
                filename.textContent = this.files[0].name + ' (' + (this.files[0].size / 1024).toFixed(1) + ' KB)';
                filename.classList.remove('hidden');
                placeholder.classList.add('hidden');
                area.classList.add('border-(--color-primary)/50');
            } else {
                filename.classList.add('hidden');
                placeholder.classList.remove('hidden');
                area.classList.remove('border-(--color-primary)/50');
            }
        });
    }

    var form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function () {
            var btn = document.getElementById('submitBtn');
            var label = btn.querySelector('[data-submit-label]');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'pointer-events-none');
            if (label) label.textContent = 'Submitting...';
        });
    }
})();
</script>
@endpush

@endsection
