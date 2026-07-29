@once
<style>
    .secure-video-player { position: relative; width: 100%; height: 100%; overflow: hidden; background: #000; }
    .secure-video-player video { width: 100%; height: 100%; max-height: 65vh; outline: none; display: block; }
    .secure-watermark {
        position: absolute;
        top: 8%;
        left: 8%;
        padding: 4px 10px;
        font-size: 12px;
        font-family: monospace;
        color: rgba(255,255,255,.55);
        background: rgba(0,0,0,.25);
        border-radius: 4px;
        pointer-events: none;
        user-select: none;
        white-space: nowrap;
        transition: top .8s ease, left .8s ease;
        z-index: 3;
    }
    .secure-video-player.player-blurred video { filter: blur(24px); }
</style>
@endonce

<div
    class="secure-video-player"
    id="securePlayer-{{ $lesson->id }}"
    data-secure-player
    data-bootstrap-url="{{ route('lessons.hls.bootstrap', $lesson) }}"
    data-user-id="{{ auth()->id() }}"
    data-user-email="{{ auth()->user()->email }}"
    oncontextmenu="return false;"
>
    <video
        controls
        autoplay
        playsinline
        controlsList="nodownload noremoteplayback"
        disablePictureInPicture
    ></video>
    <div class="secure-watermark"></div>
</div>
