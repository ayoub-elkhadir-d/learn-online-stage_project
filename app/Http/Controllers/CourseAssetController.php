<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseAssetController extends Controller
{
    private const PER_PAGE = 8;

    /**
     * Read-only timeline of resources an instructor has shared for this
     * course. There is no admin UI to create these yet — rows are inserted
     * directly for now — this controller only ever renders what exists.
     */
    public function index(Course $course)
    {
        abort_unless($course->isPurchasedBy(Auth::user()), 403);

        $assets = $course->assets()->with('user')->paginate(self::PER_PAGE, ['*'], 'page', 1);

        return response(view('courses.partials.assets-panel', [
            'course' => $course,
            'assets' => $assets,
        ])->render());
    }

    /**
     * Just the next page of timeline cards (+ updated "Load more" control),
     * appended client-side rather than replacing the whole panel.
     */
    public function loadMore(Request $request, Course $course)
    {
        abort_unless($course->isPurchasedBy(Auth::user()), 403);

        $page = max(2, $request->integer('page', 2));
        $assets = $course->assets()->with('user')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return response(view('courses.partials.assets-list-page', [
            'course' => $course,
            'assets' => $assets,
        ])->render());
    }
}
