<?php

namespace App\Services\Video;

use App\Models\Lesson;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Transcodes a lesson's uploaded source video into an AES-128 encrypted HLS
 * rendition (playlist + .ts segments), driving ffmpeg/ffprobe directly via
 * Illuminate\Process (array-args — no shell string interpolation).
 *
 * The AES key is generated fresh per lesson, handed to ffmpeg through an
 * ephemeral keyinfo file that's deleted immediately after the process exits,
 * and persisted only in the `lessons.encryption_key` column (encrypted at
 * rest via the app key). The playlist ffmpeg writes contains the literal
 * placeholder string KEY_URI_PLACEHOLDER in its #EXT-X-KEY line — the
 * streaming controller rewrites that (and every segment filename) into a
 * fresh signed URL each time the playlist is served, so nothing static or
 * guessable ever reaches the browser.
 */
class HlsTranscoder
{
    private const SEGMENT_SECONDS = 4;
    private const KEY_URI_PLACEHOLDER = 'KEY_URI_PLACEHOLDER';

    public function transcode(Lesson $lesson): void
    {
        $this->assertBinaryExecutable(config('streaming.ffmpeg_binary'), 'FFMPEG_BINARY');
        $this->assertBinaryExecutable(config('streaming.ffprobe_binary'), 'FFPROBE_BINARY');

        $disk = Storage::disk('private');

        if (! $disk->exists($lesson->video_path)) {
            throw new RuntimeException("Source video missing for lesson {$lesson->id}: {$lesson->video_path}");
        }

        $lesson->forceFill(['status' => Lesson::STATUS_PROCESSING])->save();

        $sourcePath = $disk->path($lesson->video_path);
        $hlsRelativeDir = "lessons/{$lesson->id}/hls";
        $outputDir = $disk->path($hlsRelativeDir);

        $tmpDir = $disk->path("tmp/hls-{$lesson->id}-" . Str::random(8));
        $keyPath = $tmpDir . DIRECTORY_SEPARATOR . 'key.bin';
        $keyInfoPath = $tmpDir . DIRECTORY_SEPARATOR . 'keyinfo.txt';

        try {
            @mkdir($tmpDir, 0700, true);
            @mkdir($outputDir, 0700, true);

            $key = random_bytes(16);
            file_put_contents($keyPath, $key);
            // ffmpeg's hls_key_info_file format: key URI (written verbatim into
            // the playlist), then the local path ffmpeg reads the raw key from.
            file_put_contents($keyInfoPath, self::KEY_URI_PLACEHOLDER . "\n" . $keyPath . "\n");

            $duration = $this->probeDuration($sourcePath);

            $threads = (int) config('streaming.ffmpeg_threads');

            $result = Process::timeout((int) config('streaming.ffmpeg_timeout'))->run([
                config('streaming.ffmpeg_binary'),
                '-y',
                ...($threads > 0 ? ['-threads', (string) $threads] : []),
                '-i', $sourcePath,
                '-c:v', 'libx264',
                '-preset', 'veryfast',
                '-crf', '20',
                '-g', '48',
                '-sc_threshold', '0',
                '-c:a', 'aac',
                '-b:a', '128k',
                '-hls_time', (string) self::SEGMENT_SECONDS,
                '-hls_playlist_type', 'vod',
                '-hls_key_info_file', $keyInfoPath,
                '-hls_segment_filename', $outputDir . DIRECTORY_SEPARATOR . 'seg_%05d.ts',
                $outputDir . DIRECTORY_SEPARATOR . 'playlist.m3u8',
            ]);

            if (! $result->successful()) {
                throw new RuntimeException("ffmpeg failed for lesson {$lesson->id}: " . $result->errorOutput());
            }

            $lesson->forceFill([
                'status' => Lesson::STATUS_READY,
                'duration_seconds' => $duration,
                'hls_path' => $hlsRelativeDir,
                'encryption_key' => base64_encode($key),
            ])->save();
        } catch (\Throwable $e) {
            Log::error("HLS transcode failed for lesson {$lesson->id}: {$e->getMessage()}");
            $disk->deleteDirectory($hlsRelativeDir);
            $lesson->forceFill(['status' => Lesson::STATUS_FAILED])->save();
            throw $e;
        } finally {
            @unlink($keyPath);
            @unlink($keyInfoPath);
            @rmdir($tmpDir);
        }
    }

    /**
     * On shared hosting the binary is usually an uploaded static build
     * outside of PATH — catch a missing/non-executable path here with a
     * clear message instead of letting Process fail with an opaque
     * "command not found" / permission-denied error.
     */
    private function assertBinaryExecutable(string $binary, string $envVar): void
    {
        // Bare command names (e.g. "ffmpeg") are resolved via PATH by the OS,
        // not checkable as a local file — only validate explicit paths.
        if (! str_contains($binary, DIRECTORY_SEPARATOR) && ! str_contains($binary, '/')) {
            return;
        }

        if (! is_file($binary)) {
            throw new RuntimeException("{$envVar} points to a file that doesn't exist: {$binary}");
        }

        if (! is_executable($binary)) {
            throw new RuntimeException("{$envVar} ({$binary}) is not executable — run: chmod +x {$binary}");
        }
    }

    private function probeDuration(string $sourcePath): ?int
    {
        $result = Process::timeout(30)->run([
            config('streaming.ffprobe_binary'),
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'csv=p=0',
            $sourcePath,
        ]);

        if (! $result->successful()) {
            return null;
        }

        $seconds = (float) trim($result->output());

        return $seconds > 0 ? (int) round($seconds) : null;
    }
}
