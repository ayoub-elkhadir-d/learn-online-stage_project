<?php

return [
    'back_to_course' => 'Back to course',
    'title' => 'Secure Checkout',
    'subtitle' => 'Complete your payment to get instant access to :course',

    // Already submitted
    'already_submitted_title' => 'Payment Already Submitted',
    'already_submitted_text' => "You've already submitted a payment request for this course. It's awaiting admin confirmation.",
    'sender_name' => 'Sender Account Name',
    'sender_account' => 'Sender Account Number',
    'details' => 'Details',
    'receipt' => 'Receipt',
    'back_to_course_btn' => 'Back to Course',

    // Step 1
    'step1_title' => 'Step 1 — Make the Bank Transfer',
    'step1_subtitle' => 'Send the exact course price to the account below.',
    'bank_name' => 'Bank Name',
    'account_holder' => 'Account Holder',
    'account_number' => 'Account Number',
    'iban' => 'IBAN',
    'swift' => 'SWIFT / BIC',
    'copy' => 'Copy',
    'copied' => 'Copied',
    'not_configured' => "Bank details haven't been configured yet. Please check back shortly.",
    'need_help' => 'Need help?',
    'whatsapp' => 'WhatsApp',

    'payment_notice' => 'After completing the transfer, please upload your payment receipt and enter the account information used for the transfer. We will verify your payment and activate your course as soon as possible.',

    // Step 2
    'step2_title' => 'Step 2 — Your Transfer Details',
    'step2_subtitle' => 'Tell us who sent the money, then attach your receipt.',
    'sender_name_placeholder' => 'Name on the sending bank account',
    'sender_name_hint' => 'The full name registered on the account you transferred from.',
    'sender_account_placeholder' => 'e.g. 123 456 789 0123456789 01',
    'sender_account_hint' => 'The RIB/account number you transferred from.',
    'additional_details' => 'Additional Transfer Details',
    'additional_details_placeholder' => 'e.g. Sent 1,200 MAD from CIH Bank on 15 Jan 2026',
    'additional_details_hint' => 'Sender bank, transfer date, amount, or any other note that helps us match your payment.',
    'payment_receipt' => 'Payment Receipt',
    'receipt_upload_hint' => 'Click or drag & drop to upload',
    'receipt_formats' => 'JPG, PNG or PDF — Max 5MB',

    // Step 3
    'step3_title' => 'Step 3 — Confirmation',
    'step3_subtitle' => 'Review everything, then submit your payment proof.',
    'course' => 'Course',
    'price' => 'Price',
    'uploaded_receipt' => 'Uploaded Receipt',
    'not_uploaded_yet' => 'Not uploaded yet',
    'transfer_amount' => 'Transfer Amount',
    'confirm_checkbox' => 'I confirm that the information above is correct.',
    'submit_payment' => 'Submit Payment',
    'submitting' => 'Submitting...',
    'secure_note' => 'Your information is securely submitted. Admin will confirm your payment.',

    // Flash messages
    'already_have_access' => 'You already have access to this course.',
    'purchase_created_pending' => 'Purchase created (pending). Admin will confirm your payment.',

    // Small JS-facing fragments only (copy-to-clipboard label + upload
    // widget state), not the whole file — per "don't load all translations
    // into JS".
    'js' => [
        'copy' => 'Copy',
        'copied' => 'Copied',
        'submitting' => 'Submitting...',
        'not_uploaded_yet' => 'Not uploaded yet',
    ],
];
