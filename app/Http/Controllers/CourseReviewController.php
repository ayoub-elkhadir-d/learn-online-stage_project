<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseReviewController extends Controller
{
    private const PER_PAGE = 5;

    /**
     * Renders the whole Ratings & Reviews tab: summary, write/edit form, and
     * page 1 of the list. Used for the initial tab load and after any
     * mutation, so the client only ever needs one "swap this HTML in" step.
     */
    public function index(Course $course)
    {
        return response($this->renderPanel($course));
    }

    /**
     * Create or update the current user's review for this course — a single
     * endpoint covers both since a user may only ever have one review per
     * course (enforced by the course_id/user_id unique constraint).
     */
    public function store(Request $request, Course $course)
    {
        abort_unless($course->isPurchasedBy(Auth::user()), 403, 'You need to purchase this course to leave a review.');

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        CourseReview::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => Auth::id()],
            $data
        );

        return response($this->renderPanel($course));
    }

    /**
     * Delete the current user's own review for this course.
     */
    public function destroy(Course $course)
    {
        $course->reviews()->where('user_id', Auth::id())->delete();

        return response($this->renderPanel($course));
    }

    /**
     * Just the next page of review cards (+ updated "Load more" control),
     * appended client-side rather than replacing the whole panel.
     */
    public function loadMore(Request $request, Course $course)
    {
        $page = max(2, $request->integer('page', 2));
        $myReview = $this->myReview($course);

        $reviews = $course->reviews()
            ->with('user')
            ->when($myReview, fn ($q) => $q->whereKeyNot($myReview->id))
            ->latest()
            ->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return response(view('courses.partials.reviews-list-page', [
            'course' => $course,
            'reviews' => $reviews,
        ])->render());
    }

    private function myReview(Course $course): ?CourseReview
    {
        if (! Auth::check()) {
            return null;
        }

        return $course->reviews()->where('user_id', Auth::id())->first();
    }

    private function renderPanel(Course $course): string
    {
        $summary = $course->reviews()
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(AVG(rating), 0) as average,
                SUM(rating = 5) as star5,
                SUM(rating = 4) as star4,
                SUM(rating = 3) as star3,
                SUM(rating = 2) as star2,
                SUM(rating = 1) as star1
            ')
            ->first();

        $myReview = $this->myReview($course);

        $reviews = $course->reviews()
            ->with('user')
            ->when($myReview, fn ($q) => $q->whereKeyNot($myReview->id))
            ->latest()
            ->paginate(self::PER_PAGE, ['*'], 'page', 1);

        return view('courses.partials.reviews-panel', [
            'course' => $course,
            'summary' => $summary,
            'myReview' => $myReview,
            'canReview' => $course->isPurchasedBy(Auth::user()),
            'reviews' => $reviews,
        ])->render();
    }
}
