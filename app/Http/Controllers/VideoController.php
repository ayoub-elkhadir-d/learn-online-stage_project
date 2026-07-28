<?php

namespace App\Http\Controllers;

use App\Models\CoursePurchase;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    /**
     * Stream a lesson's video to the browser with HTTP Range support.
     *
     * Videos are served through the app instead of relying on the
     * public/storage symlink, because that symlink can be missing or
     * broken on shared hosting (it doesn't survive a plain file upload,
     * and needs `php artisan storage:link` to be re-run on the server).
     * Streaming here also lets us verify the viewer actually paid for
     * the course before handing over the file, and Range support is
     * implemented by hand so seeking/scrubbing in the player works.
     */
    public function stream(Request $request, Lesson $lesson): StreamedResponse
    {
        $paid = CoursePurchase::where('user_id', Auth::id())
            ->where('course_id', $lesson->course_id)
            ->where('status', 'paid')
            ->exists();

        abort_unless($paid, 403);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($lesson->video_path), 404);

        $path = $disk->path($lesson->video_path);
        $size = filesize($path);
        $mime = $disk->mimeType($lesson->video_path) ?: 'video/mp4';

        $start = 0;
        $end = $size - 1;
        $status = 200;
        $headers = [
            'Content-Type'  => $mime,
            'Accept-Ranges' => 'bytes',
        ];

        if ($range = $request->header('Range')) {
            if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
                if ($matches[1] !== '') {
                    $start = (int) $matches[1];
                }
                if ($matches[2] !== '') {
                    $end = (int) $matches[2];
                }
                $end = min($end, $size - 1);

                if ($start <= $end) {
                    $status = 206;
                    $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
                }
            }
        }

        $length = $end - $start + 1;
        $headers['Content-Length'] = $length;

        return response()->stream(function () use ($path, $start, $length) {
            $stream = fopen($path, 'rb');
            fseek($stream, $start);
            $remaining = $length;

            while ($remaining > 0 && ! feof($stream)) {
                $read = min(8192, $remaining);
                echo fread($stream, $read);
                flush();
                $remaining -= $read;
            }

            fclose($stream);
        }, $status, $headers);
    }
}
