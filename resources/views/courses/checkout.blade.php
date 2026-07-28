<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — {{ $course->title }} — ArtiWeb</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.navbar-styles')
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f4f6fb; }

        .checkout-header {
            background: linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);
            color: white;
            padding: 2.5rem 0;
        }

        .card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .card-sm { border-radius: 12px; }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            font-size: 14px;
            padding: .6rem .85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: .3rem; }
        .form-text { font-size: 11px; color: #9ca3af; }

        .btn-submit {
            background: linear-gradient(135deg,#4f46e5,#7c3aed);
            border: none; border-radius: 12px;
            padding: .85rem; font-weight: 600; font-size: 15px;
            color: #fff; width: 100%;
            transition: all .25s;
        }
        .btn-submit:hover { opacity: .92; box-shadow: 0 6px 20px rgba(79,70,229,.35); color: #fff; transform: translateY(-1px); }

        .btn-back {
            display: inline-flex; align-items: center; gap: .4rem;
            color: rgba(255,255,255,.8); text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: all .2s;
        }
        .btn-back:hover { color: #fff; }

        .order-summary {
            background: #f9f8ff;
            border-radius: 12px;
            padding: 1.25rem;
        }
        .order-summary .label { font-size: 12px; color: #9ca3af; }
        .order-summary .value { font-size: 15px; font-weight: 600; color: #111827; }

        .receipt-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #fafafa;
        }
        .receipt-upload-area:hover {
            border-color: #4f46e5;
            background: rgba(79,70,229,.03);
        }
        .receipt-upload-area.has-file {
            border-color: #10b981;
            background: rgba(16,185,129,.04);
        }
        .receipt-upload-area i { font-size: 2rem; color: #9ca3af; margin-bottom: .5rem; }
        .receipt-upload-area.has-file i { color: #10b981; }
        .receipt-filename { font-size: 12px; color: #374151; font-weight: 500; display: none; }
        .receipt-upload-area.has-file .receipt-filename { display: block; }
        .receipt-upload-area.has-file .receipt-placeholder { display: none; }

        .step-indicator {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .step {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            flex-shrink: 0;
        }
        .step-active { background: #4f46e5; color: #fff; }
        .step-done { background: #d1fae5; color: #065f46; }
        .step-pending { background: #e5e7eb; color: #9ca3af; }
        .step-line { flex: 1; height: 2px; background: #e5e7eb; }
        .step-line.done { background: #10b981; }
        .step-label { font-size: 11px; font-weight: 500; color: #6b7280; }
    </style>
</head>
<body>

@include('partials.navbar')

<!-- Checkout Header -->
<div class="checkout-header">
    <div class="container">
        <a href="{{ route('courses.show', $course->slug) }}" class="btn-back mb-3">
            <i class="fas fa-arrow-left"></i> Back to course
        </a>
        <h3 class="fw-bold mb-1">Secure Checkout</h3>
        <p class="opacity-75 mb-0" style="font-size:14px;">Complete your payment to get instant access to <strong>{{ $course->title }}</strong></p>
    </div>
</div>

<div class="container py-4">

    @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show border-0" style="border-radius:12px;">
            <i class="fas fa-info-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0" style="border-radius:12px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Payment Form -->
        <div class="col-lg-8">
            <div class="card p-4">

                <!-- Step indicator -->
                <div class="step-indicator">
                    <div class="step step-active">1</div>
                    <div class="step-line"></div>
                    <div class="step step-pending">2</div>
                    <div class="step-line"></div>
                    <div class="step step-pending">3</div>
                    <div style="display:flex;flex-direction:column;margin-left:.75rem;">
                        <span style="font-size:12px;font-weight:600;color:#4f46e5;">Payment Details</span>
                        <span style="font-size:10px;color:#9ca3af;">Confirmation</span>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">
                    <i class="fas fa-credit-card me-2" style="color:#4f46e5;"></i>
                    Bank Transfer Payment
                </h5>

                @if($purchase && $purchase->status === 'pending')
                    <div class="text-center py-4">
                        <div style="width:70px;height:70px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-clock" style="color:#92400e;font-size:1.8rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Payment Already Submitted</h5>
                        <p class="text-muted mb-3" style="font-size:13px;">
                            You have already submitted a payment request for this course. 
                            Your payment is awaiting admin confirmation.
                        </p>
                        <div class="order-summary mb-3 text-start">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="label">Full Name</span>
                                <span class="value" style="font-size:13px;">{{ $purchase->full_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="label">RIB</span>
                                <span class="value" style="font-size:13px;">{{ $purchase->rib }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="label">Reference</span>
                                <span class="value" style="font-size:13px;">{{ $purchase->reference }}</span>
                            </div>
                            @if($purchase->receipt_path)
                            <div class="d-flex justify-content-between">
                                <span class="label">Receipt</span>
                                <a href="{{ Storage::url($purchase->receipt_path) }}" target="_blank" style="font-size:13px;color:#4f46e5;">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </div>
                            @endif
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="btn-submit d-block text-center text-decoration-none" style="width:auto;display:inline-block !important;padding:.6rem 2rem;">
                            <i class="fas fa-arrow-left me-2"></i>Back to Course
                        </a>
                    </div>
                @else
                <form method="POST" action="{{ route('courses.purchase', $course->slug) }}" enctype="multipart/form-data" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="payment_method" value="bank_transfer">

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="order-summary d-flex align-items-center gap-3">
                                <div style="width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    @if($course->cover_image_path)
                                        <img src="{{ Storage::url($course->cover_image_path) }}" style="width:56px;height:56px;object-fit:cover;border-radius:12px;">
                                    @else
                                        <i class="fas fa-graduation-cap" style="color:#6d28d9;font-size:1.3rem;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:14px;font-weight:600;color:#111827;">{{ $course->title }}</div>
                                    <div style="font-size:12px;color:#9ca3af;">{{ $course->category->name }}</div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size:18px;font-weight:700;color:#4f46e5;">{{ number_format($course->price_mad, 0, ',', ' ') }} MAD</div>
                                    <div style="font-size:10px;color:#9ca3af;">One-time payment</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user me-1" style="color:#4f46e5;"></i>
                            Full Name
                        </label>
                        <input type="text" name="full_name" class="form-control" placeholder="Your full name as registered in the bank" value="{{ old('full_name', auth()->user()->name) }}" required>
                        <div class="form-text">Enter the full name used on your bank account.</div>
                    </div>

                    <!-- RIB -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-university me-1" style="color:#4f46e5;"></i>
                            Your RIB 
                        </label>
                        <input type="text" name="rib" class="form-control" placeholder="e.g. 123 456 789 0123456789 01" value="{{ old('rib') }}" required>
                        <div class="form-text">The RIB of the account you're transferring from.</div>
                    </div>

                    <!-- Receipt Upload -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-file-image me-1" style="color:#4f46e5;"></i>
                            Receipt Screenshot
                        </label>
                        <div class="receipt-upload-area" id="receiptArea" onclick="document.getElementById('receiptInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div class="receipt-placeholder">
                                <div style="font-size:13px;font-weight:500;color:#374151;">Click to upload receipt</div>
                                <div style="font-size:11px;color:#9ca3af;">JPG, PNG or PDF — Max 5MB</div>
                            </div>
                            <div class="receipt-filename" id="receiptFilename"></div>
                        </div>
                        <input type="file" name="receipt" id="receiptInput" class="d-none" accept="image/png,image/jpeg,image/jpg,application/pdf" required>
                        <div class="form-text">Upload a clear screenshot or PDF of the payment confirmation from your bank.</div>
                    </div>

                    <button class="btn-submit" type="submit" id="submitBtn">
                        <i class="fas fa-lock me-2"></i>Submit Payment Proof — {{ number_format($course->price_mad, 0, ',', ' ') }} MAD
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1" style="color:#4f46e5;"></i>
                            Your information is securely submitted. Admin will confirm your payment.
                        </small>
                    </div>
                </form>
                @endif
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="col-lg-4">
            <div class="card p-4" style="position:sticky;top:80px;">
                <h6 class="fw-bold mb-3" style="font-size:14px;">
                    <i class="fas fa-receipt me-1"></i>
                    Order Summary
                </h6>

                <div class="order-summary mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="label">Course</span>
                        <span class="value" style="font-size:13px;">{{ $course->title }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="label">Category</span>
                        <span style="font-size:12px;color:#6b7280;">{{ $course->category->name }}</span>
                    </div>
                    <hr style="margin:.6rem 0;">
                    <div class="d-flex justify-content-between">
                        <span class="label" style="font-size:14px;">Total</span>
                        <span style="font-size:20px;font-weight:800;color:#4f46e5;">{{ number_format($course->price_mad, 0, ',', ' ') }} MAD</span>
                    </div>
                </div>

                <div style="font-size:11px;color:#9ca3af;line-height:1.7;">
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-1" style="color:#10b981;"></i>
                        One-time payment — lifetime access
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-1" style="color:#10b981;"></i>
                        Admin confirmation required
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-check-circle me-1" style="color:#10b981;"></i>
                        Secure data handling
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Receipt file name display
    const receiptInput = document.getElementById('receiptInput');
    if (receiptInput) {
        receiptInput.addEventListener('change', function(e) {
            const area = document.getElementById('receiptArea');
            const filename = document.getElementById('receiptFilename');
            if (this.files && this.files[0]) {
                filename.textContent = '📄 ' + this.files[0].name + ' (' + (this.files[0].size / 1024).toFixed(1) + ' KB)';
                area.classList.add('has-file');
            } else {
                filename.textContent = '';
                area.classList.remove('has-file');
            }
        });
    }

    // Loading state on submit
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        });
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>