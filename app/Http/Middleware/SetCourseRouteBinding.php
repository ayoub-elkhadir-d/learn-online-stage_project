<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Closure;
use Illuminate\Http\Request;

class SetCourseRouteBinding
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure route('course') is resolved to a Course model (by slug).
        if ($request->route('course')) {
            return $next($request);
        }

        $slug = $request->route('course');
        if ($slug) {
            $request->route()->setParameter('course', Course::where('slug', $slug)->firstOrFail());
        }

        return $next($request);
    }
}

