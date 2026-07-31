/**
 * Thin Vidstack Player integration for the learning page.
 *
 * All playback UI (controls, timeline, volume, speed, captions, fullscreen,
 * PiP, keyboard shortcuts, click/double-click gestures) comes from Vidstack's
 * own Default Video Layout — nothing here builds or reimplements a control
 * surface. This module only:
 *   1. registers the Vidstack custom elements,
 *   2. binds the persistent <media-player> to window.LessonProgress so
 *      position/volume/speed/completion keep saving as before,
 *   3. exposes updateLesson() so learn-page.js can swap the video source
 *      when the user navigates lessons WITHOUT destroying/recreating the
 *      player element itself.
 */
import 'vidstack/player';
import 'vidstack/player/layouts';
import 'vidstack/player/ui';

const SAVE_INTERVAL_MS = 4000;
const COMPLETE_THRESHOLD = 0.92;
const AUTO_ADVANCE_SECONDS = 5;

let playerEl = null;
let upNext = null;
let state = { lessonId: null, courseId: null, nextUrl: '', nextId: '', nextTitle: '' };
let lastSaveAt = 0;
let countdownTimer = null;
let resumeHandled = false;

function setSource(src, poster) {
    if (!playerEl) return;
    playerEl.src = src ? { src, type: 'video/mp4' } : '';
    if (poster) playerEl.poster = poster;
}

function applyStoredPreferences() {
    const LP = window.LessonProgress;
    playerEl.volume = LP.getVolume();
    playerEl.muted = LP.getMuted();
    playerEl.playbackRate = LP.getSpeed();
}

function hideUpNext() {
    clearInterval(countdownTimer);
    if (upNext) upNext.classList.add('hidden');
}

function showUpNext() {
    if (!upNext || !state.nextUrl) return;
    const titleEl = upNext.querySelector('[data-up-next-title]');
    const linkEl = upNext.querySelector('[data-up-next-link]');
    const countEl = upNext.querySelector('[data-up-next-count]');
    if (titleEl) titleEl.textContent = state.nextTitle || 'Next lesson';
    if (linkEl) {
        linkEl.href = state.nextUrl;
        linkEl.dataset.lessonId = state.nextId;
    }

    let n = AUTO_ADVANCE_SECONDS;
    if (countEl) countEl.textContent = String(n);
    upNext.classList.remove('hidden');

    clearInterval(countdownTimer);
    countdownTimer = setInterval(() => {
        n -= 1;
        if (countEl) countEl.textContent = String(Math.max(n, 0));
        if (n <= 0) {
            clearInterval(countdownTimer);
            document.dispatchEvent(new CustomEvent('lesson:auto-advance', { detail: { url: state.nextUrl, lessonId: state.nextId } }));
        }
    }, 1000);
}

function bindEvents() {
    playerEl.addEventListener('loaded-metadata', () => {
        if (!state.lessonId) return;
        window.LessonProgress.saveDuration(state.lessonId, playerEl.duration);
        document.dispatchEvent(new CustomEvent('lesson:duration-known'));

        if (!resumeHandled) {
            resumeHandled = true;
            const resumeAt = window.LessonProgress.getPosition(state.lessonId);
            if (resumeAt > 3 && resumeAt < playerEl.duration - 5) {
                playerEl.currentTime = resumeAt;
            }
        }
    });

    playerEl.addEventListener('play', () => {
        window.LessonProgress.recordActivity();
    });

    playerEl.addEventListener('time-update', () => {
        if (!state.lessonId) return;
        const now = performance.now();
        if (now - lastSaveAt < SAVE_INTERVAL_MS) return;
        lastSaveAt = now;

        window.LessonProgress.savePosition(state.lessonId, playerEl.currentTime);
        window.LessonProgress.addTimeSpent(4);
        if (state.courseId) {
            const titleParts = document.title.split(' — ');
            window.LessonProgress.setContinueLearning(window.location.href, titleParts[0] || document.title, state.courseId, titleParts[1]);
        }

        if (playerEl.duration && playerEl.currentTime / playerEl.duration > COMPLETE_THRESHOLD && state.courseId) {
            window.LessonProgress.markCompleted(state.courseId, state.lessonId);
            document.dispatchEvent(new CustomEvent('lesson:completed', { detail: { lessonId: state.lessonId } }));
        }
    });

    playerEl.addEventListener('ended', () => {
        if (!state.lessonId) return;
        window.LessonProgress.clearPosition(state.lessonId);
        if (state.courseId) {
            window.LessonProgress.markCompleted(state.courseId, state.lessonId);
            document.dispatchEvent(new CustomEvent('lesson:completed', { detail: { lessonId: state.lessonId } }));
        }
        showUpNext();
    });

    playerEl.addEventListener('volume-change', () => {
        window.LessonProgress.saveVolume(playerEl.volume);
        window.LessonProgress.saveMuted(playerEl.muted);
    });

    playerEl.addEventListener('rate-change', () => {
        window.LessonProgress.saveSpeed(playerEl.playbackRate);
    });

    upNext?.querySelector('[data-up-next-replay]')?.addEventListener('click', () => {
        hideUpNext();
        playerEl.currentTime = 0;
        playerEl.play();
    });
    upNext?.querySelector('[data-up-next-cancel]')?.addEventListener('click', hideUpNext);
}

function initPlayer(root) {
    playerEl = root.querySelector('media-player');
    upNext = root.querySelector('[data-up-next]');
    if (!playerEl) return;

    root.addEventListener('contextmenu', (e) => e.preventDefault());

    state = {
        lessonId: playerEl.dataset.lessonId || null,
        courseId: playerEl.dataset.courseId || null,
        nextUrl: playerEl.dataset.nextUrl || '',
        nextId: playerEl.dataset.nextId || '',
        nextTitle: playerEl.dataset.nextTitle || '',
    };

    applyStoredPreferences();
    bindEvents();
    resumeHandled = false;
}

/**
 * Called on every lesson navigation. Reassigns the source on the SAME
 * <media-player> element — the player is never torn down or recreated, so
 * volume/speed/UI state naturally survive the switch for free.
 */
function updateLesson({ src, poster, title, lessonId, courseId, nextUrl, nextId, nextTitle }) {
    if (!playerEl) return;

    hideUpNext();
    resumeHandled = false;
    lastSaveAt = 0;

    state = { lessonId, courseId, nextUrl: nextUrl || '', nextId: nextId || '', nextTitle: nextTitle || '' };

    if (title) playerEl.title = title;
    setSource(src, poster);

    Object.assign(playerEl.dataset, {
        lessonId: lessonId || '',
        courseId: courseId || '',
        nextUrl: nextUrl || '',
        nextId: nextId || '',
        nextTitle: nextTitle || '',
    });
}

window.LessonPlayerController = {
    init: initPlayer,
    updateLesson,
    seekTo(seconds) {
        if (!playerEl) return;
        playerEl.currentTime = seconds;
        playerEl.play?.().catch?.(() => {});
    },
    getCurrentTime() {
        return playerEl ? playerEl.currentTime : 0;
    },
    cancelAutoAdvance: hideUpNext,
};

export { initPlayer, updateLesson };
