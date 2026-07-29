<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HLS Signed URL Lifetimes
    |--------------------------------------------------------------------------
    |
    | How long the signed playlist/segment URLs and the (much shorter-lived)
    | decryption key URL stay valid for once issued to a browser.
    |
    */

    'playlist_ttl_minutes' => (int) env('STREAM_PLAYLIST_TTL', 240),

    'key_ttl_minutes' => (int) env('STREAM_KEY_TTL', 5),

    /*
    |--------------------------------------------------------------------------
    | FFmpeg / FFprobe Binaries
    |--------------------------------------------------------------------------
    |
    | On shared hosting (no system-wide ffmpeg on PATH) these should point at
    | a standalone static binary uploaded to the account, e.g.
    | /home/youruser/bin/ffmpeg — must be chmod +x and outside any publicly
    | served directory.
    |
    */

    'ffmpeg_binary' => env('FFMPEG_BINARY', 'ffmpeg'),

    'ffprobe_binary' => env('FFPROBE_BINARY', 'ffprobe'),

    /*
    |--------------------------------------------------------------------------
    | FFmpeg Process Limits
    |--------------------------------------------------------------------------
    |
    | timeout: max seconds the transcode process may run before Symfony
    | Process kills it (separate from — and usually longer than — any PHP
    | max_execution_time / web-server-level request timeout, which can still
    | cut the request short on shared hosting; see deployment notes).
    |
    | threads: passed to ffmpeg's -threads flag. Shared hosting accounts are
    | typically capped at 1-2 CPU cores (CloudLinux LVE); leaving this at 0
    | lets ffmpeg auto-detect, which can over-subscribe a throttled account.
    |
    */

    'ffmpeg_timeout' => (int) env('FFMPEG_TIMEOUT', 3600),

    'ffmpeg_threads' => (int) env('FFMPEG_THREADS', 0),

];
