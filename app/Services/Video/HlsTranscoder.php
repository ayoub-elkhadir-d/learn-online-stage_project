<?php

namespace App\Services\Video;

use App\Models\Lesson;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Transcodes a lesson's uploaded source video into an AES-128 encrypted HLS
 * rendition (playlist + .ts segments), driving ffmpeg/ffprobe directly via
 * Illuminate\Process (array-args — no shell string interpolation).
 *
 * The AES key is generated fresh per lesson (never reused — a new random
 * key and a new random filename are produced on every transcode, including
 * re-uploads) and, once ffmpeg has finished encrypting the segments with it,
 * moved into a persistent file under storage/app/private/video-keys/ — never
 * inside public/ or public_html/. Only the generated filename is stored on
 * the lesson row, not the key bytes themselves. The playlist ffmpeg writes
 * contains the literal placeholder string KEY_URI_PLACEHOLDER in its
 * #EXT-X-KEY line — the streaming controller rewrites that (and every
 * segment filename) into a fresh signed URL each time the playlist is
 * served, so nothing static or guessable ever reaches the browser.
 *
 * The entire method body — binary checks, directory checks, key generation,
 * the ffmpeg run, and output verification — is wrapped in a single
 * try/catch so no failure mode is left unrecorded: whatever throws, the
 * lesson is marked 'failed' with a full structured diagnostic (exception
 * class/message/trace, the exact ffmpeg command, its exit code, stdout and
 * stderr, the source filename, and a timestamp) persisted to
 * `lessons.encoding_error` and written to the log — never just a bare
 * "failed" with no way to tell why.
 */
class HlsTranscoder
{
    private const SEGMENT_SECONDS = 4;
    private const KEY_URI_PLACEHOLDER = 'KEY_URI_PLACEHOLDER';
    private const KEYS_DIR = 'video-keys';

    public function transcode(Lesson $lesson): void
    {
        $disk = Storage::disk('private');

        $lesson->forceFill([
            'status' => Lesson::STATUS_PROCESSING,
            'encryption_status' => Lesson::ENCRYPTION_PENDING,
            'encoding_error' => null,
        ])->save();

        $hlsRelativeDir = "lessons/{$lesson->id}/hls";
        $command = null;
        $processResult = null;
        $tmpDir = null;
        $keyPath = null;
        $keyInfoPath = null;

        try {
            $this->assertBinaryExecutable(config('streaming.ffmpeg_binary'), 'FFMPEG_BINARY');
            $this->assertBinaryExecutable(config('streaming.ffprobe_binary'), 'FFPROBE_BINARY');

            if (! $disk->exists($lesson->video_path)) {
                throw new RuntimeException("Source video missing on disk: {$lesson->video_path}");
            }

            // A re-upload never overwrites an existing key file's bytes — the
            // old key (if any) is discarded and a brand new one generated below.
            if ($lesson->encryption_key_filename) {
                $disk->delete(self::KEYS_DIR . '/' . $lesson->encryption_key_filename);
            }

            $sourcePath = $disk->path($lesson->video_path);
            $outputDir = $disk->path($hlsRelativeDir);
            $keysDir = $disk->path(self::KEYS_DIR);
            $keyFilename = Str::random(40) . '.key';
            $permanentKeyPath = $keysDir . DIRECTORY_SEPARATOR . $keyFilename;

            $tmpDir = $disk->path("tmp/hls-{$lesson->id}-" . Str::random(8));
            $keyPath = $tmpDir . DIRECTORY_SEPARATOR . 'key.bin';
            $keyInfoPath = $tmpDir . DIRECTORY_SEPARATOR . 'keyinfo.txt';

            $this->ensureWritableDirectory($tmpDir);
            $this->ensureWritableDirectory($outputDir);
            $this->ensureWritableDirectory($keysDir);

            $key = random_bytes(16);

            if (@file_put_contents($keyPath, $key) === false) {
                throw new RuntimeException("Could not write temporary key file: {$keyPath}");
            }

            // ffmpeg's hls_key_info_file format: key URI (written verbatim into
            // the playlist), then the local path ffmpeg reads the raw key from.
            $keyInfoContents = self::KEY_URI_PLACEHOLDER . "\n" . $keyPath . "\n";
            if (@file_put_contents($keyInfoPath, $keyInfoContents) === false) {
                throw new RuntimeException("Could not write keyinfo file: {$keyInfoPath}");
            }

            $duration = $this->probeDuration($sourcePath, $lesson);

            $threads = (int) config('streaming.ffmpeg_threads');

            $command = [
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
            ];

            $processResult = Process::timeout((int) config('streaming.ffmpeg_timeout'))->run($command);

            if (! $processResult->successful()) {
                throw new RuntimeException(sprintf(
                    'ffmpeg exited with code %d: %s',
                    $processResult->exitCode(),
                    trim($processResult->errorOutput()) ?: '(no stderr output)'
                ));
            }

            // ffmpeg can, in rare edge cases, exit 0 without having actually
            // produced usable output (e.g. zero-duration/corrupt input) — never
            // mark a lesson ready without confirming the files are really there.
            $playlistPath = $outputDir . DIRECTORY_SEPARATOR . 'playlist.m3u8';
            if (! is_file($playlistPath) || filesize($playlistPath) === 0) {
                throw new RuntimeException("ffmpeg reported success but playlist.m3u8 is missing or empty: {$playlistPath}");
            }

            $segments = glob($outputDir . DIRECTORY_SEPARATOR . '*.ts') ?: [];
            if (count($segments) === 0) {
                throw new RuntimeException("ffmpeg reported success but no .ts segments were found in: {$outputDir}");
            }

            // Move (not copy) the exact key ffmpeg just used to encrypt the
            // segments into its permanent home — the served key must match
            // the one baked into the .ts files byte-for-byte.
            if (! @rename($keyPath, $permanentKeyPath)) {
                throw new RuntimeException("Could not move key file into place: {$keyPath} -> {$permanentKeyPath}");
            }

            $lesson->forceFill([
                'status' => Lesson::STATUS_READY,
                'duration_seconds' => $duration,
                'hls_path' => $hlsRelativeDir,
                'encryption_key_filename' => $keyFilename,
                'encryption_status' => Lesson::ENCRYPTION_ENCRYPTED,
                'encryption_algorithm' => 'AES-128',
                'encoding_error' => null,
            ])->save();
        } catch (Throwable $e) {
            $errorContext = [
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'command' => $command ? implode(' ', $command) : null,
                'exit_code' => $processResult?->exitCode(),
                'ffmpeg_stdout' => $processResult?->output(),
                'ffmpeg_stderr' => $processResult?->errorOutput(),
                'stack_trace' => $e->getTraceAsString(),
                'upload_filename' => $lesson->video_path ? basename($lesson->video_path) : null,
                'lesson_id' => $lesson->id,
                'failed_at' => now()->toDateTimeString(),
            ];

            Log::error("HLS transcode failed for lesson {$lesson->id}", $errorContext);

            $disk->deleteDirectory($hlsRelativeDir);

            $lesson->forceFill([
                'status' => Lesson::STATUS_FAILED,
                'encryption_status' => Lesson::ENCRYPTION_FAILED,
                'encoding_error' => json_encode($errorContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ])->save();

            throw $e;
        } finally {
            if ($keyPath) {
                @unlink($keyPath);
            }
            if ($keyInfoPath) {
                @unlink($keyInfoPath);
            }
            if ($tmpDir) {
                @rmdir($tmpDir);
            }
        }
    }

    /**
     * On shared hosting the binary is usually an uploaded static build
     * outside of PATH — catch a missing/non-executable path here with a
     * clear message instead of letting Process fail with an opaque
     * "command not found" error. Also surfaces the underlying PHP warning
     * (e.g. "open_basedir restriction in effect") when the check itself
     * fails for a reason other than the file genuinely not existing.
     */
    private function assertBinaryExecutable(string $binary, string $envVar): void
    {
        // Bare command names (e.g. "ffmpeg") are resolved via PATH by the OS,
        // not checkable as a local file — only validate explicit paths.
        if (! str_contains($binary, '/') && ! str_contains($binary, '\\')) {
            return;
        }

        error_clear_last();
        $exists = @is_file($binary);
        if (! $exists) {
            $warning = error_get_last();
            $hint = $warning ? " (PHP warning: {$warning['message']})" : '';
            throw new RuntimeException("{$envVar} points to a file that doesn't exist or isn't readable by PHP: {$binary}{$hint}");
        }

        error_clear_last();
        $executable = @is_executable($binary);
        if (! $executable) {
            $warning = error_get_last();
            $hint = $warning ? " (PHP warning: {$warning['message']})" : '';
            throw new RuntimeException("{$envVar} ({$binary}) is not executable — run: chmod +x {$binary}{$hint}");
        }
    }

    /**
     * Creates the directory if missing and confirms PHP can actually write
     * to it — recursive mkdir() failures are suppressed by design (the "@"),
     * so silently trusting it succeeded is exactly how a permissions problem
     * turns into a much more confusing failure two steps later.
     */
    private function ensureWritableDirectory(string $path): void
    {
        if (! is_dir($path)) {
            error_clear_last();
            if (! @mkdir($path, 0700, true) && ! is_dir($path)) {
                $warning = error_get_last();
                $hint = $warning ? ": {$warning['message']}" : '';
                throw new RuntimeException("Could not create directory {$path}{$hint}");
            }
        }

        if (! is_writable($path)) {
            throw new RuntimeException("Directory exists but is not writable by PHP: {$path}");
        }
    }

    private function probeDuration(string $sourcePath, Lesson $lesson): ?int
    {
        $command = [
            config('streaming.ffprobe_binary'),
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'csv=p=0',
            $sourcePath,
        ];

        $result = Process::timeout(30)->run($command);

        if (! $result->successful()) {
            // Non-fatal: duration is metadata only, not required to encode or
            // play the video — but still logged with full detail so a
            // failing ffprobe doesn't disappear silently (task: never hide
            // an ffprobe failure, even when it isn't the reason transcoding
            // as a whole failed).
            Log::warning("ffprobe failed for lesson {$lesson->id} (non-fatal — duration_seconds will be null)", [
                'command' => implode(' ', $command),
                'exit_code' => $result->exitCode(),
                'ffprobe_stdout' => $result->output(),
                'ffprobe_stderr' => $result->errorOutput(),
            ]);

            return null;
        }

        $seconds = (float) trim($result->output());

        return $seconds > 0 ? (int) round($seconds) : null;
    }
}
