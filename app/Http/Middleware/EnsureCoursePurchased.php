<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;

/**
 * Gate-backed access check for lesson content — delegates to
 * App\Policies\CoursePolicy::learn(), the same policy VideoController uses
 * for the actual video bytes, so there is exactly one place that defines
 * "does this user have access to this course" (Course::isPurchasedBy(),
 * which only ever returns true for a CoursePurchase with status === 'paid').
 * A rejected or cancelled purchase therefore loses learn-page access on the
 * very next request — there is nothing to invalidate/expire, the check is
 * re-run from the database every time.
 */
class EnsureCoursePurchased
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var Course|null $course */
        $course = $request->route('course');
        abort_unless($course instanceof Course, 404);

        if (Gate::denies('learn', $course)) {
            return redirect()->route('courses.show', $course->slug)
                ->with('status', 'You need to purchase this course to access the content.');
        }

        return $next($request);
    }
}

