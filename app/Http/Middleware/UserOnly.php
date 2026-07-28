<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.courses.index');
        }

        return $next($request);
    }
}
