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

    .player-loading {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,.4);
        z-index: 3;
        opacity: 1;
        transition: opacity .2s ease;
        pointer-events: none;
    }
    .player-loading.is-hidden { opacity: 0; }
    .player-spinner {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,.25);
        border-top-color: #fff;
        animation: player-spin .8s linear infinite;
    }
    @keyframes player-spin { to { transform: rotate(360deg); } }
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
    <div class="player-loading" data-player-loading>
        <div class="player-spinner"></div>
    </div>
</div>
