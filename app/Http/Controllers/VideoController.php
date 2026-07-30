<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a lesson's video with HTTP Range support. Videos live on the
 * 'private' disk (storage/app/private/videos), which is never reachable
 * through a public URL — this controller is the only path to the bytes,
 * and it never reveals the real filesystem path to the client. Every
 * request re-checks that the viewer is authenticated and actually paid for
 * the course; nothing is cached client-side or by intermediate proxies.
 */
class VideoController extends Controller
{
    public function stream(Request $request, Lesson $lesson): StreamedResponse
    {
        $this->authorize('learn', $lesson->course);

        $disk = Storage::disk('private');

        abort_unless(
            $lesson->video_path && $disk->exists($lesson->video_path),
            404,
            'Video file not found.'
        );

        $path = $disk->path($lesson->video_path);

        abort_unless(is_readable($path), 500, 'Video file is not readable.');

        $size = filesize($path);
        $mime = $disk->mimeType($lesson->video_path) ?: 'video/mp4';

        $start = 0;
        $end = $size - 1;
        $status = 200;

        $headers = [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
            'Content-Disposition' => 'inline',
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
            if ($stream === false) {
                return;
            }

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
