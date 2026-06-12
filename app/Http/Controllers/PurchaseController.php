<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function purchase(Request $request, string $slug)
    {
        $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
        ]);

        $course = Course::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        $purchase = CoursePurchase::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($purchase && $purchase->status === 'paid') {
            return redirect()->route('courses.show', $course->slug)
                ->with('status', 'You already have access to this course.');
        }

        // MVP: create/update purchase as pending.
        if (!$purchase) {
            $purchase = CoursePurchase::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'payment_method' => $request->input('payment_method', 'bank_transfer'),
                'status' => 'pending',
                'reference' => $request->input('reference'),
                'purchased_at' => now(),
            ]);
        } else {
            $purchase->update([
                'payment_method' => $request->input('payment_method', 'bank_transfer'),
                'status' => 'pending',
                'reference' => $request->input('reference'),
                'purchased_at' => now(),
            ]);
        }

        return redirect()->route('courses.show', $course->slug)
            ->with('status', 'Purchase created (pending). Admin will confirm your payment.');
    }
}

