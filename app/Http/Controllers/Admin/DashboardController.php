<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Read-only aggregate queries only — no writes, no new tables.
     */
    public function index()
    {
        $stats = [
            'courses'          => Course::count(),
            'categories'       => Category::count(),
            'users'            => User::where('role', 'user')->count(),
            'pending_payments' => CoursePurchase::where('status', 'pending')->count(),
            'revenue'          => CoursePurchase::where('status', 'paid')
                ->join('courses', 'courses.id', '=', 'course_purchases.course_id')
                ->sum('courses.price_mad'),
        ];

        $recentPayments = CoursePurchase::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $recentEnrollments = CoursePurchase::with(['user', 'course'])
            ->where('status', 'paid')
            ->latest('purchased_at')
            ->take(5)
            ->get();

        $latestCourses = Course::with('category')
            ->latest()
            ->take(5)
            ->get();

        $latestUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentPayments', 'recentEnrollments', 'latestCourses', 'latestUsers'
        ));
    }
}
