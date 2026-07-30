/**
 * Lightweight, non-DRM download deterrents for the lesson video player.
 * None of this stops a determined user (screen recording always works,
 * and a saved copy of the page defeats all of it) — it raises the bar
 * against casual right-click-save/drag-out, which is the realistic goal
 * for plain authenticated MP4 streaming.
 */
(function () {
    'use strict';

    document.addEventListener('contextmenu', function (e) {
        if (e.target.closest('[data-secure-player]')) {
            e.preventDefault();
        }
    });

    document.addEventListener('dragstart', function (e) {
        if (e.target.closest('[data-secure-player]')) {
            e.preventDefault();
        }
    });
})();
