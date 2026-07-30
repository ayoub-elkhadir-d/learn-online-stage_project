/**
 * Custom HTML5 video player for the learning page. No plugin library — built
 * directly on the native <video> element, which keeps it small and keeps
 * the existing `lessons.video` streaming route untouched.
 *
 * Lifecycle: one instance is created per lesson view and destroyed on the
 * existing AJAX lesson-swap in learn.blade.php (see LessonPlayer.mount /
 * .destroy below), matching the destroy-before-swap / init-after-swap
 * pattern already used on this page.
 */

const ICONS = {
    play: '<polygon points="6 3 20 12 6 21 6 3"/>',
    pause: '<rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/>',
    'volume-2': '<path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><path d="M16 9a5 5 0 0 1 0 6"/><path d="M19.364 18.364a9 9 0 0 0 0-12.728"/>',
    'volume-1': '<path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><path d="M16 9a5 5 0 0 1 0 6"/>',
    'volume-x': '<path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><line x1="22" x2="16" y1="9" y2="15"/><line x1="16" x2="22" y1="9" y2="15"/>',
    maximize: '<path d="M8 3H5a2 2 0 0 0-2 2v3"/><path d="M21 8V5a2 2 0 0 0-2-2h-3"/><path d="M3 16v3a2 2 0 0 0 2 2h3"/><path d="M16 21h3a2 2 0 0 0 2-2v-3"/>',
    minimize: '<path d="M8 3v3a2 2 0 0 1-2 2H3"/><path d="M21 8h-3a2 2 0 0 1-2-2V3"/><path d="M3 16h3a2 2 0 0 1 2 2v3"/><path d="M16 21v-3a2 2 0 0 1 2-2h3"/>',
    pip: '<path d="M8 4H4a2 2 0 0 0-2 2v3"/><path d="M4 16v2a2 2 0 0 0 2 2h1"/><path d="M9 20h6"/><path d="M20 14v4a2 2 0 0 1-2 2h-1"/><rect x="12" y="8" width="10" height="7" rx="2"/>',
    settings: '<path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/><circle cx="12" cy="12" r="3"/>',
    'rotate-ccw': '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
    'rotate-cw': '<path d="M21 12a9 9 0 1 1-9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>',
    check: '<path d="M20 6 9 17l-5-5"/>',
    x: '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
    captions: '<rect width="18" height="14" x="3" y="5" rx="2" ry="2"/><path d="M7 15h4M7 11h2m4 4h4m-6-4h6"/>',
    loader: '<line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/>',
    'alert-triangle': '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
    'bookmark-plus': '<path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/><line x1="12" y1="7" x2="12" y2="13"/><line x1="9" y1="10" x2="15" y2="10"/>',
    bookmark: '<path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>',
};

function icon(name, cls) {
    return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="${cls || 'w-5 h-5'}">${ICONS[name] || ''}</svg>`;
}

function formatTime(totalSeconds) {
    return window.LessonProgress.formatTime(totalSeconds);
}

const SPEEDS = [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2];

class LessonPlayer {
    constructor(root) {
        this.root = root;
        this.video = root.querySelector('video');
        this.lessonId = root.dataset.lessonId;
        this.courseId = root.dataset.courseId;
        this.nextUrl = root.dataset.nextUrl || '';
        this.nextId = root.dataset.nextId || '';
        this.nextTitle = root.dataset.nextTitle || '';

        if (!this.video) return;

        this._hideTimer = null;
        this._countdownTimer = null;
        this._menuOpen = null;
        this._savedActivityToday = false;
        this._lastSaveTime = 0;

        this.buildUI();
        this.bindVideoEvents();
        this.bindControlEvents();
        this.bindKeyboard();
        this.applyPreferences();
    }

    // ---- UI construction ----------------------------------------------

    buildUI() {
        this.video.removeAttribute('controls');
        this.video.setAttribute('playsinline', '');

        const hasTracks = this.video.querySelectorAll('track').length > 0;
        const qualities = this.parseQualities();

        this.overlay = document.createElement('div');
        this.overlay.className = 'absolute inset-0 flex flex-col justify-between select-none';
        this.overlay.innerHTML = `
            <div data-skeleton class="absolute inset-0 z-30 flex items-center justify-center bg-[#0c0c0c]">
                <div class="h-10 w-10 animate-spin text-white/70">${icon('loader', 'h-10 w-10')}</div>
            </div>

            <div data-buffering class="absolute inset-0 z-20 hidden items-center justify-center bg-black/20">
                <div class="h-10 w-10 animate-spin text-white">${icon('loader', 'h-10 w-10')}</div>
            </div>

            <div data-error class="absolute inset-0 z-40 hidden flex-col items-center justify-center gap-3 bg-[#0c0c0c] text-center px-6">
                <div class="text-(--color-danger)">${icon('alert-triangle', 'h-10 w-10')}</div>
                <p class="text-sm text-white/80">This video couldn't be loaded.</p>
                <button type="button" data-retry class="btn-primary text-sm">Try again</button>
            </div>

            <div data-end-screen class="absolute inset-0 z-40 hidden flex-col items-center justify-center gap-3 sm:gap-4 bg-black/85 text-center px-4 sm:px-6 backdrop-blur-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Lesson complete</p>
                ${this.nextUrl ? `
                <p class="max-w-full text-base sm:text-lg font-bold text-white">Up next: ${this.escapeHtml(this.nextTitle)}</p>
                <p data-countdown-text class="text-sm text-white/60">Starting in <span data-countdown-n>5</span>s</p>
                <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                    <button type="button" data-replay class="btn-secondary !bg-white/10 !border-white/20 !text-white text-sm">${icon('rotate-ccw', 'h-4 w-4')} Replay</button>
                    <a href="${this.nextUrl}" data-lesson-nav data-lesson-id="${this.nextId}" data-cancel-autonext-link class="btn-primary text-sm">Next Lesson ${icon('rotate-cw', 'hidden')}</a>
                    <button type="button" data-cancel-autonext class="text-xs font-medium text-white/50 underline hover:text-white/80">Cancel</button>
                </div>
                ` : `
                <p class="max-w-full text-base sm:text-lg font-bold text-white">Nice work — you finished this lesson!</p>
                <button type="button" data-replay class="btn-primary text-sm">${icon('rotate-ccw', 'h-4 w-4')} Watch again</button>
                `}
            </div>

            <button type="button" data-big-play aria-label="Play"
                class="absolute inset-0 z-10 m-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/95 text-(--color-primary) shadow-lift transition-transform hover:scale-105">
                ${icon('play', 'h-7 w-7 ml-0.5')}
            </button>

            <div data-topbar class="relative z-10 flex items-center justify-end p-3 opacity-0 transition-opacity"></div>

            <div data-controlbar class="relative z-10 flex flex-col gap-1 sm:gap-1.5 bg-gradient-to-t from-black/80 via-black/40 to-transparent px-2 sm:px-3 pb-1.5 sm:pb-2 pt-8 opacity-0 transition-opacity">

                <div data-timeline class="group/tl relative h-5 sm:h-3 w-full cursor-pointer flex items-center">
                    <div class="relative h-1 w-full rounded-full bg-white/25 group-hover/tl:h-1.5 transition-all">
                        <div data-buffered-fill class="absolute inset-y-0 left-0 rounded-full bg-white/40" style="width:0%"></div>
                        <div data-played-fill class="absolute inset-y-0 left-0 rounded-full bg-(--color-primary)" style="width:0%"></div>
                        <div data-bookmarks-track class="absolute inset-0"></div>
                        <div data-scrub-handle class="absolute top-1/2 h-3 w-3 -translate-y-1/2 -translate-x-1/2 rounded-full bg-(--color-primary) opacity-0 group-hover/tl:opacity-100 transition-opacity" style="left:0%"></div>
                    </div>
                    <div data-hover-preview class="pointer-events-none absolute bottom-4 hidden -translate-x-1/2 flex-col items-center">
                        <div data-preview-thumb class="mb-1 hidden h-16 w-28 overflow-hidden rounded-md border border-white/20 bg-black/80 bg-cover bg-center"></div>
                        <span data-preview-time class="rounded bg-black/90 px-1.5 py-0.5 text-[11px] font-medium text-white">0:00</span>
                    </div>
                </div>

                <div class="flex items-center gap-0.5 sm:gap-1.5">
                    <button type="button" data-play-pause aria-label="Play/Pause" class="player-btn">${icon('play', 'h-5 w-5')}</button>
                    <button type="button" data-skip-back aria-label="Replay 10 seconds" class="player-btn hidden sm:inline-flex" title="Replay 10s">${icon('rotate-ccw', 'h-[18px] w-[18px]')}</button>
                    <button type="button" data-skip-forward aria-label="Skip 10 seconds" class="player-btn hidden sm:inline-flex" title="Skip 10s">${icon('rotate-cw', 'h-[18px] w-[18px]')}</button>

                    <div class="group/vol flex items-center">
                        <button type="button" data-mute aria-label="Mute" class="player-btn">${icon('volume-2', 'h-5 w-5')}</button>
                        <input data-volume type="range" min="0" max="1" step="0.05" aria-label="Volume"
                            class="w-0 group-hover/vol:w-16 focus:w-16 transition-[width] duration-200 accent-(--color-primary) ml-0.5">
                    </div>

                    <span data-time class="hidden text-xs font-medium tabular-nums text-white/90 sm:inline">0:00 / 0:00</span>

                    <div class="ml-auto flex items-center gap-0.5 sm:gap-1">
                        <button type="button" data-bookmark-add aria-label="Add bookmark" title="Bookmark this moment" class="player-btn">${icon('bookmark-plus', 'h-[18px] w-[18px]')}</button>

                        ${hasTracks ? `<button type="button" data-captions aria-label="Captions" class="player-btn" title="Captions">${icon('captions', 'h-[18px] w-[18px]')}</button>` : ''}

                        <div class="relative" data-menu-wrapper="speed">
                            <button type="button" data-menu-toggle="speed" class="player-btn text-xs font-semibold" title="Playback speed">1x</button>
                            <div data-menu="speed" class="absolute bottom-full right-0 mb-2 hidden w-28 overflow-hidden rounded-lg bg-[#1c1c1c] py-1 shadow-lift text-sm">
                                ${SPEEDS.map(s => `<button type="button" data-speed-option="${s}" class="flex w-full items-center justify-between px-3 py-1.5 text-white/80 hover:bg-white/10">${s}x <span data-speed-check="${s}" class="hidden text-(--color-primary)">${icon('check', 'h-3.5 w-3.5')}</span></button>`).join('')}
                            </div>
                        </div>

                        <div class="relative" data-menu-wrapper="settings">
                            <button type="button" data-menu-toggle="settings" aria-label="Settings" class="player-btn">${icon('settings', 'h-[18px] w-[18px]')}</button>
                            <div data-menu="settings" class="absolute bottom-full right-0 mb-2 hidden w-44 overflow-hidden rounded-lg bg-[#1c1c1c] py-1 shadow-lift text-sm text-white/80">
                                <div class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-white/40">Quality</div>
                                ${qualities.length > 0
                                    ? qualities.map(q => `<button type="button" data-quality-option="${q.src}" class="flex w-full items-center justify-between px-3 py-1.5 hover:bg-white/10">${q.label}</button>`).join('')
                                    : `<div class="flex items-center justify-between px-3 py-1.5 text-white/50">Auto <span class="text-(--color-primary)">${icon('check', 'h-3.5 w-3.5')}</span></div>`}
                            </div>
                        </div>

                        <button type="button" data-pip aria-label="Picture in picture" class="player-btn hidden">${icon('pip', 'h-[18px] w-[18px]')}</button>
                        <button type="button" data-fullscreen aria-label="Fullscreen" class="player-btn">${icon('maximize', 'h-[18px] w-[18px]')}</button>
                    </div>
                </div>
            </div>
        `;
        this.root.appendChild(this.overlay);

        // Scoped control-button styling (kept local to avoid bloating app.css with one-off player rules).
        if (!document.getElementById('player-btn-style')) {
            const style = document.createElement('style');
            style.id = 'player-btn-style';
            style.textContent = '.player-btn{display:inline-flex;align-items:center;justify-content:center;height:2.25rem;width:2.25rem;flex-shrink:0;border-radius:.5rem;color:rgba(255,255,255,.9);transition:background-color .15s} .player-btn:hover{background:rgba(255,255,255,.15)}';
            document.head.appendChild(style);
        }

        const $ = sel => this.overlay.querySelector(sel);
        this.el = {
            skeleton: $('[data-skeleton]'),
            buffering: $('[data-buffering]'),
            error: $('[data-error]'),
            endScreen: $('[data-end-screen]'),
            bigPlay: $('[data-big-play]'),
            topbar: $('[data-topbar]'),
            controlbar: $('[data-controlbar]'),
            timeline: $('[data-timeline]'),
            bufferedFill: $('[data-buffered-fill]'),
            playedFill: $('[data-played-fill]'),
            scrubHandle: $('[data-scrub-handle]'),
            bookmarksTrack: $('[data-bookmarks-track]'),
            hoverPreview: $('[data-hover-preview]'),
            previewThumb: $('[data-preview-thumb]'),
            previewTime: $('[data-preview-time]'),
            playPause: $('[data-play-pause]'),
            skipBack: $('[data-skip-back]'),
            skipForward: $('[data-skip-forward]'),
            mute: $('[data-mute]'),
            volume: $('[data-volume]'),
            time: $('[data-time]'),
            bookmarkAdd: $('[data-bookmark-add]'),
            captions: $('[data-captions]'),
            pip: $('[data-pip]'),
            fullscreen: $('[data-fullscreen]'),
            countdownN: $('[data-countdown-n]'),
        };
    }

    parseQualities() {
        try {
            return JSON.parse(this.root.dataset.qualities || '[]');
        } catch (e) {
            return [];
        }
    }

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    // ---- Preferences ----------------------------------------------------

    applyPreferences() {
        const LP = window.LessonProgress;
        this.video.volume = LP.getVolume();
        this.video.muted = LP.getMuted();
        this.video.playbackRate = LP.getSpeed();
        this.el.volume.value = this.video.volume;
        this.updateSpeedLabel();
        this.updateVolumeIcon();

        const resumeAt = LP.getPosition(this.lessonId);
        if (resumeAt > 3) {
            this.video.addEventListener('loadedmetadata', () => {
                if (resumeAt < this.video.duration - 5) this.video.currentTime = resumeAt;
            }, { once: true });
        }

        if (document.pictureInPictureEnabled) this.el.pip.classList.remove('hidden');

        this.renderBookmarks();
    }

    // ---- Video element events --------------------------------------------

    bindVideoEvents() {
        const v = this.video;

        v.addEventListener('loadedmetadata', () => {
            this.el.skeleton.classList.add('hidden');
            this.updateTime();
            window.LessonProgress.saveDuration(this.lessonId, v.duration);
            // Let the sidebar refresh the just-discovered duration for THIS
            // lesson immediately, instead of waiting for the next navigation.
            document.dispatchEvent(new CustomEvent('lesson:duration-known'));
        });

        // The stream controller flushes in small chunks, which can fire brief
        // `waiting` events under completely normal playback. Debounce the
        // buffering overlay so those don't flash — only show it if a stall
        // actually persists past 300ms, and cancel on any sign of progress.
        const hideBuffering = () => {
            clearTimeout(this._bufferingTimer);
            this.el.buffering.classList.add('hidden');
        };
        v.addEventListener('waiting', () => {
            clearTimeout(this._bufferingTimer);
            this._bufferingTimer = setTimeout(() => this.el.buffering.classList.remove('hidden'), 300);
        });
        v.addEventListener('playing', () => {
            hideBuffering();
            this.el.error.classList.add('hidden');
        });
        v.addEventListener('canplay', hideBuffering);

        v.addEventListener('error', () => {
            this.el.skeleton.classList.add('hidden');
            this.el.error.classList.remove('hidden');
        });

        v.addEventListener('play', () => {
            this.setPlayIcon(false);
            this.el.bigPlay.classList.add('opacity-0', 'pointer-events-none');
            this.startHideTimer();
            window.LessonProgress.recordActivity();
        });

        v.addEventListener('pause', () => {
            this.setPlayIcon(true);
            this.el.bigPlay.classList.remove('opacity-0', 'pointer-events-none');
            this.showControls();
        });

        v.addEventListener('timeupdate', () => {
            this.updateTime();
            this.updateBuffered();
            hideBuffering(); // timeupdate firing proves we're not actually stalled

            const now = performance.now();
            if (now - this._lastSaveTime > 4000) {
                this._lastSaveTime = now;
                window.LessonProgress.savePosition(this.lessonId, v.currentTime);
                window.LessonProgress.addTimeSpent(4);
                if (this.courseId) {
                    window.LessonProgress.setContinueLearning(window.location.href, document.title.split(' — ')[0] || document.title);
                }
            }

            if (v.duration && v.currentTime / v.duration > 0.92 && this.courseId) {
                window.LessonProgress.markCompleted(this.courseId, this.lessonId);
                this.markSidebarComplete();
            }
        });

        v.addEventListener('ended', () => {
            window.LessonProgress.clearPosition(this.lessonId);
            if (this.courseId) window.LessonProgress.markCompleted(this.courseId, this.lessonId);
            this.markSidebarComplete();
            this.showEndScreen();
        });

        v.addEventListener('volumechange', () => {
            window.LessonProgress.saveVolume(v.volume);
            window.LessonProgress.saveMuted(v.muted);
            this.updateVolumeIcon();
        });

        v.addEventListener('ratechange', () => {
            window.LessonProgress.saveSpeed(v.playbackRate);
            this.updateSpeedLabel();
        });
    }

    markSidebarComplete() {
        const item = document.querySelector(`[data-lesson-nav][data-lesson-id="${this.lessonId}"]`);
        if (item) item.querySelector('[data-lesson-check]')?.classList.remove('hidden');
    }

    // ---- Control interactions --------------------------------------------

    bindControlEvents() {
        const v = this.video;
        const el = this.el;

        el.bigPlay.addEventListener('click', () => this.togglePlay());
        el.playPause.addEventListener('click', () => this.togglePlay());
        el.skipBack.addEventListener('click', () => { v.currentTime = Math.max(0, v.currentTime - 10); });
        el.skipForward.addEventListener('click', () => { v.currentTime = Math.min(v.duration || 0, v.currentTime + 10); });

        el.mute.addEventListener('click', () => { v.muted = !v.muted; });
        el.volume.addEventListener('input', () => {
            v.volume = parseFloat(el.volume.value);
            v.muted = v.volume === 0;
        });

        el.bookmarkAdd.addEventListener('click', () => {
            window.LessonProgress.addBookmark(this.lessonId, v.currentTime);
            this.renderBookmarks();
            // renderBookmarks() above only redraws the small timeline markers —
            // the visible bookmarks LIST lives in the page-level script
            // (learn.blade.php), which listens for this to update instantly
            // instead of only refreshing on the next lesson navigation.
            document.dispatchEvent(new CustomEvent('lesson:bookmarks-changed', { detail: { lessonId: this.lessonId } }));
        });

        if (el.captions) {
            el.captions.addEventListener('click', () => {
                const track = v.textTracks[0];
                if (!track) return;
                const active = track.mode === 'showing';
                track.mode = active ? 'hidden' : 'showing';
                el.captions.classList.toggle('text-(--color-primary)', !active);
            });
        }

        el.pip.addEventListener('click', async () => {
            try {
                if (document.pictureInPictureElement) await document.exitPictureInPicture();
                else await v.requestPictureInPicture();
            } catch (e) { /* unsupported in this browser context — no-op */ }
        });

        el.fullscreen.addEventListener('click', () => this.toggleFullscreen());
        this._onFullscreenChange = () => {
            const isFs = document.fullscreenElement === this.root;
            el.fullscreen.innerHTML = icon(isFs ? 'minimize' : 'maximize', 'h-[18px] w-[18px]');
        };
        document.addEventListener('fullscreenchange', this._onFullscreenChange);

        // Timeline: click/drag to seek, hover to preview.
        let scrubbing = false;
        const seekFromEvent = (e) => {
            const rect = el.timeline.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const ratio = Math.min(1, Math.max(0, (clientX - rect.left) / rect.width));
            return ratio;
        };
        el.timeline.addEventListener('pointerdown', (e) => {
            scrubbing = true;
            v.currentTime = seekFromEvent(e) * (v.duration || 0);
        });

        // Hover preview is scoped to the timeline element itself — it only
        // needs to run (and only pays the getBoundingClientRect() layout
        // cost) while the pointer is actually over the timeline, not on
        // every mouse movement across the whole page.
        el.timeline.addEventListener('pointermove', (e) => {
            if (!v.duration) return;
            const ratio = seekFromEvent(e);
            el.hoverPreview.classList.remove('hidden');
            el.hoverPreview.style.left = `${ratio * 100}%`;
            el.previewTime.textContent = formatTime(ratio * v.duration);
            // Hover-preview thumbnail architecture: wire a sprite sheet via
            // root.dataset.previewSprite (background-image + background-position
            // math keyed to `ratio`). No sprite is configured for this dataset
            // yet, so the thumb box stays hidden — this is the extension point.
            if (this.root.dataset.previewSprite) {
                el.previewThumb.classList.remove('hidden');
                el.previewThumb.style.backgroundImage = `url(${this.root.dataset.previewSprite})`;
            } else {
                el.previewThumb.classList.add('hidden');
            }
        });
        el.timeline.addEventListener('pointerleave', () => {
            if (!scrubbing) el.hoverPreview.classList.add('hidden');
        });

        // Dragging can continue past the timeline's own bounds, so this one
        // genuinely needs to listen on window — kept as cheap as possible
        // (early-return, no layout read at all) whenever not actively
        // scrubbing, which is true for the vast majority of playback time.
        this._onWindowPointerMove = (e) => {
            if (!scrubbing) return;
            v.currentTime = seekFromEvent(e) * (v.duration || 0);
        };
        this._onWindowPointerUp = () => { scrubbing = false; };
        window.addEventListener('pointermove', this._onWindowPointerMove);
        window.addEventListener('pointerup', this._onWindowPointerUp);

        // Speed / settings menus
        this.overlay.querySelectorAll('[data-menu-toggle]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const key = btn.dataset.menuToggle;
                const menu = this.overlay.querySelector(`[data-menu="${key}"]`);
                const isOpen = this._menuOpen === key;
                this.overlay.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
                menu.classList.toggle('hidden', isOpen);
                this._menuOpen = isOpen ? null : key;
            });
        });
        this.overlay.querySelectorAll('[data-speed-option]').forEach(btn => {
            btn.addEventListener('click', () => { v.playbackRate = parseFloat(btn.dataset.speedOption); });
        });
        this._onDocumentClick = () => {
            this.overlay.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
            this._menuOpen = null;
        };
        document.addEventListener('click', this._onDocumentClick);

        // Auto-hide controls
        this.root.addEventListener('pointermove', () => this.showControls());
        this.root.addEventListener('pointerleave', () => { if (!v.paused) this.startHideTimer(200); });

        // End screen
        this.overlay.querySelector('[data-replay]')?.addEventListener('click', () => {
            this.hideEndScreen();
            v.currentTime = 0;
            v.play();
        });
        this.overlay.querySelector('[data-cancel-autonext]')?.addEventListener('click', () => this.cancelAutoNext());
        this.overlay.querySelector('[data-cancel-autonext-link]')?.addEventListener('click', () => this.cancelAutoNext());

        this.overlay.querySelector('[data-retry]')?.addEventListener('click', () => {
            this.el.error.classList.add('hidden');
            v.load();
        });
    }

    togglePlay() {
        if (this.video.paused) this.video.play().catch(() => {});
        else this.video.pause();
    }

    toggleFullscreen() {
        if (document.fullscreenElement === this.root) document.exitFullscreen();
        else this.root.requestFullscreen?.();
    }

    setPlayIcon(paused) {
        this.el.playPause.innerHTML = icon(paused ? 'play' : 'pause', 'h-5 w-5');
    }

    updateVolumeIcon() {
        const v = this.video;
        const name = (v.muted || v.volume === 0) ? 'volume-x' : v.volume < 0.5 ? 'volume-1' : 'volume-2';
        this.el.mute.innerHTML = icon(name, 'h-5 w-5');
        this.el.volume.value = v.muted ? 0 : v.volume;
    }

    updateSpeedLabel() {
        const rate = this.video.playbackRate;
        const toggle = this.overlay.querySelector('[data-menu-toggle="speed"]');
        if (toggle) toggle.textContent = `${rate}x`;
        this.overlay.querySelectorAll('[data-speed-check]').forEach(el => {
            el.classList.toggle('hidden', parseFloat(el.dataset.speedCheck) !== rate);
        });
    }

    updateTime() {
        const v = this.video;
        if (!isFinite(v.duration)) return;
        const ratio = v.duration ? v.currentTime / v.duration : 0;
        this.el.playedFill.style.width = `${ratio * 100}%`;
        this.el.scrubHandle.style.left = `${ratio * 100}%`;
        this.el.time.textContent = `${formatTime(v.currentTime)} / ${formatTime(v.duration)}`;
    }

    updateBuffered() {
        const v = this.video;
        if (!v.duration || v.buffered.length === 0) return;
        const end = v.buffered.end(v.buffered.length - 1);
        this.el.bufferedFill.style.width = `${Math.min(100, (end / v.duration) * 100)}%`;
    }

    renderBookmarks() {
        const list = window.LessonProgress.getBookmarks(this.lessonId);
        this.el.bookmarksTrack.innerHTML = '';
        const duration = this.video.duration;
        if (!duration || !isFinite(duration)) return;
        list.forEach(b => {
            const mark = document.createElement('button');
            mark.type = 'button';
            mark.title = b.label;
            mark.className = 'absolute top-1/2 h-1.5 w-1.5 -translate-y-1/2 -translate-x-1/2 rounded-full bg-(--color-accent)';
            mark.style.left = `${Math.min(100, (b.time / duration) * 100)}%`;
            mark.addEventListener('click', (e) => { e.stopPropagation(); this.video.currentTime = b.time; });
            this.el.bookmarksTrack.appendChild(mark);
        });
    }

    // ---- Controls visibility ----------------------------------------------

    showControls() {
        this.el.controlbar.classList.remove('opacity-0');
        this.el.topbar.classList.remove('opacity-0');
        this.root.style.cursor = '';
        this.startHideTimer();
    }

    startHideTimer(delay = 2600) {
        clearTimeout(this._hideTimer);
        if (this.video.paused) return;
        this._hideTimer = setTimeout(() => {
            if (this._menuOpen) return;
            this.el.controlbar.classList.add('opacity-0');
            this.el.topbar.classList.add('opacity-0');
            this.root.style.cursor = 'none';
        }, delay);
    }

    // ---- End screen / auto-next --------------------------------------------

    showEndScreen() {
        this.el.endScreen.classList.remove('hidden');
        this.el.endScreen.classList.add('flex');
        this.el.controlbar.classList.add('opacity-0');

        if (!this.nextUrl || !this.el.countdownN) return;
        let n = 5;
        this.el.countdownN.textContent = n;
        this._countdownTimer = setInterval(() => {
            n -= 1;
            if (this.el.countdownN) this.el.countdownN.textContent = Math.max(n, 0);
            if (n <= 0) {
                clearInterval(this._countdownTimer);
                this.overlay.querySelector('[data-lesson-nav]')?.click();
            }
        }, 1000);
    }

    hideEndScreen() {
        this.el.endScreen.classList.add('hidden');
        this.el.endScreen.classList.remove('flex');
        this.cancelAutoNext();
    }

    cancelAutoNext() {
        clearInterval(this._countdownTimer);
        const text = this.overlay.querySelector('[data-countdown-text]');
        if (text) text.textContent = 'Autoplay canceled';
    }

    // ---- Keyboard shortcuts ------------------------------------------------

    bindKeyboard() {
        this._keyHandler = (e) => {
            if (!this.isRelevantTarget(e)) return;
            switch (e.key) {
                case ' ':
                    e.preventDefault();
                    this.togglePlay();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.video.currentTime = Math.max(0, this.video.currentTime - 5);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.video.currentTime = Math.min(this.video.duration || 0, this.video.currentTime + 5);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.video.volume = Math.min(1, this.video.volume + 0.1);
                    this.video.muted = false;
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.video.volume = Math.max(0, this.video.volume - 0.1);
                    break;
                case 'f': case 'F':
                    this.toggleFullscreen();
                    break;
                case 'm': case 'M':
                    this.video.muted = !this.video.muted;
                    break;
            }
        };
        document.addEventListener('keydown', this._keyHandler);
    }

    isRelevantTarget(e) {
        const tag = (e.target.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea') return false;
        return true;
    }

    // ---- Lifecycle ----------------------------------------------------------

    destroy() {
        clearTimeout(this._hideTimer);
        clearTimeout(this._bufferingTimer);
        clearInterval(this._countdownTimer);

        // Every one of these was registered on window/document (not on this
        // player's own DOM subtree), so they survive the AJAX innerHTML swap
        // and must be removed explicitly — otherwise each lesson switch
        // leaked a full set that kept firing forever against a stale video/
        // element closure, with the pointermove handler's layout read being
        // the most expensive one. That accumulation was the real cause of
        // playback stalling more and more the longer a session went on.
        if (this._keyHandler) document.removeEventListener('keydown', this._keyHandler);
        if (this._onFullscreenChange) document.removeEventListener('fullscreenchange', this._onFullscreenChange);
        if (this._onDocumentClick) document.removeEventListener('click', this._onDocumentClick);
        if (this._onWindowPointerMove) window.removeEventListener('pointermove', this._onWindowPointerMove);
        if (this._onWindowPointerUp) window.removeEventListener('pointerup', this._onWindowPointerUp);

        if (this.video && !this.video.paused) {
            window.LessonProgress.savePosition(this.lessonId, this.video.currentTime);
            this.video.pause();
        }
    }
}

// ---- Right-click / drag-out deterrents (carried over from the old secure-player.js) ----
document.addEventListener('contextmenu', (e) => {
    if (e.target.closest('[data-secure-player]')) e.preventDefault();
});
document.addEventListener('dragstart', (e) => {
    if (e.target.closest('[data-secure-player]')) e.preventDefault();
});

let activePlayer = null;

function mountLessonPlayer(root) {
    if (!root) return activePlayer;
    // Idempotent: never recreate the player for the lesson that's already
    // mounted and still attached to the DOM (progress ticks, notes/bookmark
    // edits, sidebar toggling, etc. must never trigger a rebuild — only an
    // actual lesson change does, which always comes with a fresh DOM node).
    if (activePlayer && activePlayer.lessonId === root.dataset.lessonId && activePlayer.root.isConnected) {
        return activePlayer;
    }
    if (activePlayer) activePlayer.destroy();
    activePlayer = new LessonPlayer(root);
    return activePlayer;
}

function destroyLessonPlayer() {
    if (activePlayer) {
        activePlayer.destroy();
        activePlayer = null;
    }
}

window.LessonPlayerController = {
    mount: mountLessonPlayer,
    destroy: destroyLessonPlayer,
    seekTo(seconds) {
        if (activePlayer) {
            activePlayer.video.currentTime = seconds;
            activePlayer.video.play().catch(() => {});
        }
    },
    // Re-draws the small timeline bookmark markers from current localStorage
    // state without touching the video element itself — used after a
    // bookmark is removed from the sidebar list.
    refreshBookmarks() {
        if (activePlayer) activePlayer.renderBookmarks();
    },
};

export { mountLessonPlayer, destroyLessonPlayer };
