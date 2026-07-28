<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category')->latest()->paginate(15);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.courses.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price_mad'   => 'required|integer|min:0',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('covers', 'public');
        }

        unset($data['cover_image']);
        Course::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.form', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price_mad'   => 'required|integer|min:0',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('covers', 'public');
        }

        unset($data['cover_image']);
        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }
}
