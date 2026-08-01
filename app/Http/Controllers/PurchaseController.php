<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function purchase(Request $request, string $slug)
    {
        $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'full_name'      => 'required|string|max:255',
            'rib'            => 'required|string|max:255',
            'reference'      => 'nullable|string|max:255',
            'receipt'        => 'required|file|image|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $course = Course::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        $purchase = CoursePurchase::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($purchase && $purchase->status === 'paid') {
            return redirect()->route('courses.show', $course->slug)
                ->with('status', __('checkout.already_have_access'));
        }

        // Store receipt file securely
        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        $data = [
            'user_id'        => $user->id,
            'course_id'      => $course->id,
            'payment_method' => $request->input('payment_method', 'bank_transfer'),
            'full_name'      => $request->input('full_name'),
            'rib'            => $request->input('rib'),
            'reference'      => $request->input('reference'),
            'receipt_path'   => $receiptPath,
            'status'         => 'pending',
            'purchased_at'   => now(),
        ];

        if (!$purchase) {
            CoursePurchase::create($data);
        } else {
            // Delete old receipt if exists
            if ($purchase->receipt_path) {
                Storage::disk('public')->delete($purchase->receipt_path);
            }
            $purchase->update($data);
        }

        return redirect()->route('courses.show', $course->slug)
            ->with('status', __('checkout.purchase_created_pending'));
    }
}