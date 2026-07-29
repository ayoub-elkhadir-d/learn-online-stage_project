<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Serves lesson video as encrypted HLS. Every action re-checks the viewer
 * actually paid for the course (via CoursePolicy::learn) — a leaked/expired
 * signed URL is worthless both after its TTL and to anyone who never
 * enrolled, since the policy check runs independently of the signature.
 *
 * Flow: bootstrap (session-auth only) hands out a signed playlist URL ->
 * playlist (signed) is read off disk and rewritten so every segment/key line
 * becomes its own freshly-signed URL -> segment/key (signed) stream the
 * actual bytes. Nothing permanent or guessable ever appears in the page.
 */
class VideoStreamController extends Controller
{
    public function bootstrap(Request $request, Lesson $lesson)
    {
        $this->authorize('learn', $lesson->course);

        if (! $lesson->isReady()) {
            return response()->json(['message' => 'This video is still processing.'], 409);
        }

        $playlistUrl = URL::temporarySignedRoute(
            'lessons.hls.playlist',
            now()->addMinutes((int) config('streaming.playlist_ttl_minutes')),
            ['lesson' => $lesson->id]
        );

        return response()->json(['playlistUrl' => $playlistUrl]);
    }

    public function playlist(Request $request, Lesson $lesson)
    {
        $this->authorize('learn', $lesson->course);

        abort_unless($lesson->isReady() && $lesson->hls_path, 404);

        $disk = Storage::disk('private');
        $playlistFile = $lesson->hls_path . '/playlist.m3u8';

        abort_unless($disk->exists($playlistFile), 404);

        $playlistTtl = now()->addMinutes((int) config('streaming.playlist_ttl_minutes'));
        $keyUrl = URL::temporarySignedRoute(
            'lessons.hls.key',
            now()->addMinutes((int) config('streaming.key_ttl_minutes')),
            ['lesson' => $lesson->id]
        );

        $lines = explode("\n", $disk->get($playlistFile));

        $rewritten = array_map(function (string $line) use ($lesson, $playlistTtl, $keyUrl) {
            $line = rtrim($line, "\r");

            if (str_starts_with($line, '#EXT-X-KEY')) {
                return preg_replace('/URI="[^"]*"/', 'URI="' . $keyUrl . '"', $line);
            }

            if ($line !== '' && ! str_starts_with($line, '#')) {
                return URL::temporarySignedRoute('lessons.hls.segment', $playlistTtl, [
                    'lesson' => $lesson->id,
                    'segment' => basename($line),
                ]);
            }

            return $line;
        }, $lines);

        return response(implode("\n", $rewritten), 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function segment(Request $request, Lesson $lesson, string $segment)
    {
        $this->authorize('learn', $lesson->course);

        abort_unless($lesson->hls_path, 404);

        $disk = Storage::disk('private');
        $relativePath = $lesson->hls_path . '/' . $segment;

        abort_unless($disk->exists($relativePath), 404);

        return $disk->response($relativePath, null, [
            'Content-Type' => 'video/mp2t',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function key(Request $request, Lesson $lesson)
    {
        $this->authorize('learn', $lesson->course);

        abort_unless($lesson->encryption_key, 404);

        $raw = base64_decode($lesson->encryption_key);

        return response($raw, 200, [
            'Content-Type' => 'application/octet-stream',
            'Cache-Control' => 'no-store, private',
            'Content-Length' => (string) strlen($raw),
        ]);
    }
}
