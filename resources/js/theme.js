/**
 * Theme toggle: persists an explicit choice to localStorage, otherwise
 * follows the OS preference live. Applying the class itself happens
 * synchronously pre-paint via partials/theme-init.blade.php — this module
 * only wires up the toggle button(s) and keeps things in sync afterward.
 */
(function () {
    'use strict';

    function apply(isDark) {
        document.documentElement.classList.toggle('dark', isDark);
    }

    function setTheme(theme) {
        localStorage.setItem('theme', theme);
        apply(theme === 'dark');
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-theme-toggle]');
        if (!btn) return;
        const isDark = document.documentElement.classList.contains('dark');
        setTheme(isDark ? 'light' : 'dark');
    });

    const media = window.matchMedia('(prefers-color-scheme: dark)');
    media.addEventListener('change', function (e) {
        if (!localStorage.getItem('theme')) {
            apply(e.matches);
        }
    });
})();
