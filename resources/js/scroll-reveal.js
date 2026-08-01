/**
 * Scroll-reveal: plain IntersectionObserver, no external library.
 * Elements marked [data-animate] fade/translate into place once, the first
 * time they enter the viewport — never re-animates on scroll-back. Styling
 * itself (opacity/transform/transition) lives in app.css; this file only
 * toggles the .is-visible class, so it's cheap even with many elements on
 * the page. Skipped entirely under prefers-reduced-motion, where the CSS
 * already renders elements fully visible with no transition.
 */
(function () {
    'use strict';

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var targets = document.querySelectorAll('[data-animate]');
    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(function (el) { observer.observe(el); });
})();
