<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Video\HlsTranscoder;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $video = $request->file('video');
        unset($data['video']);
        $data['sort_order'] = $data['sort_order'] ?? ($course->lessons()->max('sort_order') + 1);

        $lesson = $course->lessons()->create($data + ['video_path' => '']);
        $lesson->update(['video_path' => $this->storeVideo($video, $lesson)]);

        $message = $this->transcode($lesson);

        if ($request->wantsJson()) {
            return response()->json([
                'message'  => $message,
                'redirect' => route('admin.courses.lessons.index', $course),
            ]);
        }

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', $message);
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

        $video = $request->file('video');
        unset($data['video']);

        $lesson->fill($data);

        $message = 'Lesson updated successfully.';

        if ($video) {
            Storage::disk('private')->delete($lesson->video_path);
            if ($lesson->hls_path) {
                Storage::disk('private')->deleteDirectory($lesson->hls_path);
            }
            $lesson->video_path = $this->storeVideo($video, $lesson);
            $lesson->save();
            $message = $this->transcode($lesson);
        } else {
            $lesson->save();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message'  => $message,
                'redirect' => route('admin.courses.lessons.index', $course),
            ]);
        }

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', $message);
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        Storage::disk('private')->delete($lesson->video_path);
        if ($lesson->hls_path) {
            Storage::disk('private')->deleteDirectory($lesson->hls_path);
        }
        $lesson->delete();

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson deleted.');
    }

    /**
     * Store the uploaded video under a human-readable name (lesson title + id)
     * instead of the framework's default random hash filename.
     *
     * Stored on the 'private' disk (storage/app/private/lessons), which has
     * no public URL at all. It's only ever read server-side, by the HLS
     * transcoder — never served directly to a browser.
     */
    private function storeVideo(UploadedFile $video, Lesson $lesson): string
    {
        $extension = $video->getClientOriginalExtension() ?: $video->extension();
        $filename = Str::slug($lesson->title) . '-' . $lesson->id . '.' . $extension;

        return $video->storeAs('lessons/' . $lesson->id, $filename, 'private');
    }

    /**
     * Transcode the lesson's freshly-uploaded source into encrypted HLS,
     * synchronously (no queue worker configured for this app). A failure
     * here doesn't lose the upload — the lesson row and source file are
     * kept, just flagged 'failed', so the admin can retry by re-saving.
     */
    private function transcode(Lesson $lesson): string
    {
        try {
            app(HlsTranscoder::class)->transcode($lesson);

            return 'Lesson saved and video processed successfully.';
        } catch (\Throwable $e) {
            report($e);

            return 'Lesson saved, but video processing failed. Please try re-uploading the video.';
        }
    }
}
