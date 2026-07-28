<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        $lessons = $course->lessons()->orderBy('sort_order')->get();
        return view('admin.lessons.index', compact('course', 'lessons'));
    }

    public function create(Course $course)
    {
        return view('admin.lessons.form', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'required|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo|max:204800',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['video_path'] = $request->file('video')->store('lessons', 'public');
        $data['sort_order'] = $data['sort_order'] ?? ($course->lessons()->max('sort_order') + 1);
        unset($data['video']);

        $course->lessons()->create($data);

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson added successfully.');
    }

    public function edit(Course $course, Lesson $lesson)
    {
        return view('admin.lessons.form', compact('course', 'lesson'));
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo|max:204800',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('video')) {
            Storage::disk('public')->delete($lesson->video_path);
            $data['video_path'] = $request->file('video')->store('lessons', 'public');
        }

        unset($data['video']);
        $lesson->update($data);

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        Storage::disk('public')->delete($lesson->video_path);
        $lesson->delete();

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson deleted.');
    }
}
