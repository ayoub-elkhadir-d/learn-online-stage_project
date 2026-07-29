/**
 * Secure lesson video player: bootstraps a signed HLS playlist via fetch
 * (never a raw storage path in the DOM), plays it through hls.js, and layers
 * on UI deterrents (watermark, context-menu/keyboard blocking, DevTools
 * heuristic, pause-on-blur). None of this is real DRM — a technical user
 * with DevTools open can still inspect network traffic. It raises the bar
 * against casual downloading (IDM, DownloadHelper, right-click-save), which
 * is the realistic threat here.
 *
 * Only one lesson plays at a time in this app, so a single module-level
 * `current` instance is torn down/recreated across the AJAX lesson-swap
 * flow in learn.blade.php via SecurePlayer.destroy() / .init().
 */
(function () {
    'use strict';

    var current = null; // { hls, video, wrapperEl, watermarkTimer }

    function fetchBootstrap(url) {
        return fetch(url, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('bootstrap request failed: ' + res.status);
            }
            return res.json();
        });
    }

    function attachHls(video, playlistUrl) {
        if (window.Hls && window.Hls.isSupported()) {
            var hls = new window.Hls({
                xhrSetup: function (xhr) {
                    xhr.withCredentials = true;
                },
            });

            hls.loadSource(playlistUrl);
            hls.attachMedia(video);

            hls.on(window.Hls.Events.MANIFEST_PARSED, function () {
                video.play().catch(function () {
                    // Autoplay blocked without a user gesture — controls remain usable.
                });
            });

            hls.on(window.Hls.Events.ERROR, function (event, data) {
                if (!data.fatal) return;
                switch (data.type) {
                    case window.Hls.ErrorTypes.NETWORK_ERROR:
                        hls.startLoad();
                        break;
                    case window.Hls.ErrorTypes.MEDIA_ERROR:
                        hls.recoverMediaError();
                        break;
                    default:
                        hls.destroy();
                        break;
                }
            });

            return hls;
        }

        // Safari and other browsers with native HLS support.
        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = playlistUrl;
            video.addEventListener('loadedmetadata', function () {
                video.play().catch(function () {});
            }, { once: true });
        }

        return null;
    }

    function startWatermark(wrapperEl) {
        var el = wrapperEl.querySelector('.secure-watermark');
        if (!el) return null;

        var userId = wrapperEl.dataset.userId || '';
        var userEmail = wrapperEl.dataset.userEmail || '';

        function place() {
            el.textContent = 'User #' + userId + ' · ' + userEmail + ' · ' + new Date().toLocaleTimeString();
            el.style.top = (5 + Math.random() * 80) + '%';
            el.style.left = (5 + Math.random() * 70) + '%';
        }

        place();
        return setInterval(place, 5000 + Math.random() * 5000);
    }

    function init(wrapperEl) {
        if (!wrapperEl) return;

        destroy(); // only one instance active at a time

        var video = wrapperEl.querySelector('video');
        var bootstrapUrl = wrapperEl.dataset.bootstrapUrl;
        if (!video || !bootstrapUrl) return;

        current = {
            hls: null,
            video: video,
            wrapperEl: wrapperEl,
            watermarkTimer: startWatermark(wrapperEl),
        };

        fetchBootstrap(bootstrapUrl)
            .then(function (data) {
                if (current && current.wrapperEl === wrapperEl && data.playlistUrl) {
                    current.hls = attachHls(video, data.playlistUrl);
                }
            })
            .catch(function () {
                // Not ready / not enrolled / expired — leave the player empty.
            });
    }

    function destroy() {
        if (!current) return;

        if (current.watermarkTimer) clearInterval(current.watermarkTimer);

        if (current.hls) {
            try { current.hls.destroy(); } catch (e) { /* noop */ }
        }

        if (current.video) {
            current.video.pause();
            current.video.removeAttribute('src');
            current.video.load();
        }

        current = null;
    }

    // ---- Global UI deterrents (registered once, apply to whichever player is active) ----

    document.addEventListener('contextmenu', function (e) {
        if (e.target.closest('[data-secure-player]')) {
            e.preventDefault();
        }
    });

    document.addEventListener('keydown', function (e) {
        var blockF12 = e.key === 'F12';
        var blockDevtoolsCombo = (e.ctrlKey || e.metaKey) && e.shiftKey && ['KeyI', 'KeyJ', 'KeyC'].indexOf(e.code) !== -1;
        var blockViewSource = (e.ctrlKey || e.metaKey) && !e.shiftKey && e.code === 'KeyU';
        var blockSave = (e.ctrlKey || e.metaKey) && !e.shiftKey && e.code === 'KeyS';

        if (blockF12 || blockDevtoolsCombo || blockViewSource || blockSave) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    function pauseActive() {
        if (current && current.video) current.video.pause();
    }

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) pauseActive();
    });
    window.addEventListener('blur', pauseActive);

    // Best-effort DevTools heuristic — docked panels shift the outer/inner
    // viewport delta. Trivially bypassed (undocked/separate-window devtools),
    // it's a deterrent, not a boundary.
    var devtoolsOpen = false;
    setInterval(function () {
        var threshold = 160;
        var open = (window.outerWidth - window.innerWidth > threshold) ||
                   (window.outerHeight - window.innerHeight > threshold);

        if (open !== devtoolsOpen) {
            devtoolsOpen = open;
            if (current && current.wrapperEl) {
                current.wrapperEl.classList.toggle('player-blurred', open);
                if (open) pauseActive();
            }
        }
    }, 1000);

    window.SecurePlayer = { init: init, destroy: destroy };
})();
