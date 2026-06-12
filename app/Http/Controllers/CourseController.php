<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $categories = Category::with('courses')->get();
        return view('courses.index', compact('categories'));
    }

    public function show(string $slug)
    {
        $course = Course::with('category')->where('slug', $slug)->firstOrFail();

        return view('courses.show', [
            'course' => $course,
        ]);
    }
}

