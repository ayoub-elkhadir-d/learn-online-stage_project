<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CoursePurchase;

class EnsureCoursePurchased
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        /** @var Course $course */
        $course = $request->route('course');
        if (!$course) {
            abort(404);
        }

        $purchase = CoursePurchase::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$purchase || $purchase->status !== 'paid') {
            return redirect()->route('courses.show', $course->slug)
                ->with('status', 'You need to purchase this course to access the content.');
        }

        return $next($request);
    }
}

