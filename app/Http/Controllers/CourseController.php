<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePurchase;
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

    public function learn(string $slug)
    {
        $course = Course::with(['lessons' => fn($q) => $q->orderBy('sort_order')])->where('slug', $slug)->firstOrFail();

        $purchase = CoursePurchase::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'paid')
            ->first();

        if (!$purchase) {
            return redirect()->route('courses.show', $slug)
                ->with('status', 'You need to complete your purchase to access this course.');
        }

        $lessons = $course->lessons;
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

        return view('courses.checkout', compact('course', 'purchase'));
    }
}