@once
<style>
    .secure-video-player {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        max-height: 65vh;
        overflow: hidden;
        background: #000;
        margin: 0 auto;
    }
    .secure-video-player video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        outline: none;
        display: block;
    }
    .secure-video-player.player-blurred video { filter: blur(24px); }
</style>
@endonce

<div
    class="secure-video-player"
    id="securePlayer-{{ $lesson->id }}"
    data-secure-player
    data-bootstrap-url="{{ route('lessons.hls.bootstrap', $lesson) }}"
    oncontextmenu="return false;"
>
    <video
        controls
        autoplay
        playsinline
        controlsList="nodownload noremoteplayback"
        disablePictureInPicture
    ></video>
</div>
