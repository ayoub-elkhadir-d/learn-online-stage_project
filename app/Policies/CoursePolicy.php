<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Whether the user may access lesson content (video streaming, learn page)
     * for this course — i.e. they have a paid CoursePurchase for it.
     */
    public function learn(User $user, Course $course): bool
    {
        return $course->isPurchasedBy($user);
    }
}
