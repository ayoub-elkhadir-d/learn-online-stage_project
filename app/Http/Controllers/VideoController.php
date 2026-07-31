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
 * the course. The response is cacheable by the viewer's own browser only
 * (Cache-Control: private) so re-seeking/replaying doesn't re-stream
 * already-downloaded bytes; shared caches/CDNs/proxies must not store it.
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
            // 'private' still forbids shared/CDN caches from storing this,
            // but lets the viewer's own browser reuse already-downloaded
            // ranges (seeking back, replaying) instead of re-fetching them
            // over the network every time — 'no-store' was forcing a full
            // re-stream from disk on every seek/replay.
            'Cache-Control' => 'private, max-age=86400',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($path)).' GMT',
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
            // Long videos can take longer to fully stream than PHP's default
            // execution time limit allows; that would truncate the response
            // mid-file and present to the player as a stall/failed load.
            set_time_limit(0);

            $stream = fopen($path, 'rb');
            if ($stream === false) {
                return;
            }

            fseek($stream, $start);
            $remaining = $length;
            // 1MB chunks instead of 8KB — the previous size meant a 500MB
            // video needed ~60k fread()+flush() round-trips, which throttled
            // real throughput far below what the connection could sustain
            // and starved the player's buffer.
            $chunkSize = 1024 * 1024;

            while ($remaining > 0 && ! feof($stream)) {
                if (connection_aborted()) {
                    break;
                }

                $read = min($chunkSize, $remaining);
                echo fread($stream, $read);

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                $remaining -= $read;
            }

            fclose($stream);
        }, $status, $headers);
    }
}
