<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = CoursePurchase::with(['user', 'course.category'])
            ->latest()
            ->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }

    public function approve(CoursePurchase $purchase)
    {
        if ($purchase->status === 'paid') {
            return redirect()->route('admin.payments.index')
                ->with('success', 'This payment is already approved.');
        }

        $purchase->update([
            'status' => 'paid',
            'purchased_at' => now(),
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', "Payment approved. User '{$purchase->user->name}' now has access to '{$purchase->course->title}'.");
    }

    public function reject(CoursePurchase $purchase)
    {
        if ($purchase->status === 'paid') {
            return redirect()->route('admin.payments.index')
                ->with('success', 'Cannot reject an already approved payment.');
        }

        $purchase->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment request rejected and removed.');
    }
}