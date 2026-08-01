<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = CoursePurchase::with(['user', 'course.category'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->q;
                $query->where(function ($q) use ($term) {
                    $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
                      ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(CoursePurchase $purchase)
    {
        $purchase->load(['user', 'course.category']);

        return view('admin.payments.show', ['payment' => $purchase]);
    }

    public function approve(CoursePurchase $purchase)
    {
        if ($purchase->status === 'paid') {
            return redirect()->route('admin.payments.index')
                ->with('success', __('admin.flash_already_approved'));
        }

        $purchase->update([
            'status' => 'paid',
            'purchased_at' => now(),
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', __('admin.flash_payment_approved', [
                'user' => $purchase->user->name,
                'course' => $purchase->course->title,
            ]));
    }

    public function reject(CoursePurchase $purchase)
    {
        if ($purchase->status === 'paid') {
            return redirect()->route('admin.payments.index')
                ->with('success', __('admin.flash_cannot_reject_approved'));
        }

        // Soft status change — the record (and any receipt/reference the
        // user submitted) is kept as history, matching Approve/Cancel.
        $purchase->update(['status' => 'rejected']);

        return redirect()->route('admin.payments.index')
            ->with('success', __('admin.flash_payment_rejected'));
    }

    public function cancel(CoursePurchase $purchase)
    {
        if ($purchase->status !== 'paid') {
            return redirect()->route('admin.payments.index')
                ->with('success', __('admin.flash_only_approved_can_cancel'));
        }

        // Revokes access without deleting anything: Course::isPurchasedBy()
        // only grants access when status === 'paid', so flipping the status
        // is all that's needed. The row — receipt, reference, history — stays.
        $purchase->update(['status' => 'cancelled']);

        return redirect()->route('admin.payments.index')
            ->with('success', __('admin.flash_access_revoked', [
                'user' => $purchase->user->name,
                'course' => $purchase->course->title,
            ]));
    }
}
