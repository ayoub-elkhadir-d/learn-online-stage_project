<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $courses = Course::with('category')
            ->when($request->category, fn($q) => $q->whereHas('category', fn($q) => $q->where('slug', $request->category)))
            ->latest()
            ->paginate(9);

        return view('courses.index', compact('categories', 'courses'));
    }

    public function show(string $slug)
    {
        $course = Course::with('category')->where('slug', $slug)->firstOrFail();

        $purchase = null;
        if (Auth::check()) {
            $purchase = CoursePurchase::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();
        }

        return view('courses.show', compact('course', 'purchase'));
    }

    /**
     * Access is already enforced before this method runs — the
     * 'course.purchased' route middleware (App\Http\Middleware\
     * EnsureCoursePurchased) denies the request via CoursePolicy::learn()
     * for anyone without a 'paid' CoursePurchase, so this method only ever
     * runs for a viewer who is already authorized.
     */
    public function learn(Course $course)
    {
        $lessons = $course->lessons()->orderBy('sort_order')->get();

        $purchase = CoursePurchase::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'paid')
            ->first();

        $currentLesson = $lessons->firstWhere('id', request('lesson')) ?? $lessons->first();

        return view('courses.learn', compact('course', 'lessons', 'currentLesson', 'purchase'));
    }

    public function checkout(string $slug)
    {
        $course = Course::with('category')->where('slug', $slug)->firstOrFail();

        $purchase = CoursePurchase::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        // If already paid, redirect to show
        if ($purchase && $purchase->status === 'paid') {
            return redirect()->route('courses.show', $course->slug)
                ->with('status', 'You already have access to this course.');
        }

        // Always the single admin-managed row — nothing here is influenced
        // by the request (no id/query param selects it), so there is no way
        // for a client to change which bank account is displayed.
        $bankInfo = PaymentSetting::current();

        return view('courses.checkout', compact('course', 'purchase', 'bankInfo'));
    }
}