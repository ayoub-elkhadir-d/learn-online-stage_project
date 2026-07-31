/**
 * Page-level orchestration for the learning page: sidebar (search, active
 * state, progress), AJAX lesson navigation, the Overview/Bookmarks/Ratings
 * tabs below the player, and the bookmarks + reviews panels inside them.
 * The Vidstack <media-player> element itself is never touched here beyond
 * calling window.LessonPlayerController.updateLesson() — see player.js for
 * everything playback-related.
 */

const prefetchCache = new Map();

function $(sel, root = document) {
    return root.querySelector(sel);
}
function $$(sel, root = document) {
    return Array.from(root.querySelectorAll(sel));
}

function currentPlayerEl() {
    return document.querySelector('#playerRegion media-player');
}
function currentLessonId() {
    return currentPlayerEl()?.dataset.lessonId || null;
}
function currentCourseId() {
    return currentPlayerEl()?.dataset.courseId || window.__courseId || null;
}

// ---- Sidebar ---------------------------------------------------------------

function activateSidebarItem(lessonId) {
    $$('.lesson-row').forEach(el => {
        el.dataset.active = String(el.dataset.lessonId) === String(lessonId);
    });
    const active = document.querySelector(`.lesson-row[data-lesson-id="${lessonId}"]`);
    if (active) active.scrollIntoView({ block: 'nearest' });
    closeMobileSidebar();
}

function paintCompleted() {
    const courseId = currentCourseId();
    $$('[data-lesson-nav]').forEach(item => {
        const check = item.querySelector('[data-lesson-check]');
        if (!check) return;
        // Inline style (not the `hidden` utility class) so this doesn't
        // depend on Tailwind's generated rule order to beat `flex`.
        check.style.display = window.LessonProgress.isCompleted(courseId, item.dataset.lessonId) ? 'flex' : 'none';
    });
}

function paintDurations() {
    $$('[data-lesson-duration]').forEach(el => {
        const d = window.LessonProgress.getDuration(el.dataset.lessonDuration);
        if (d) {
            el.textContent = window.LessonProgress.formatTime(d);
            el.classList.remove('skeleton');
        }
    });
}

function paintProgress() {
    const courseId = currentCourseId();
    const total = Number(window.__totalLessons || 0);
    const completed = window.LessonProgress.completedCount(courseId);
    const pct = total > 0 ? Math.round((completed / total) * 100) : 0;

    $$('[data-progress-bar]').forEach(el => { el.style.width = pct + '%'; });
    $$('[data-progress-pct]').forEach(el => { el.textContent = pct + '%'; });
    $$('[data-progress-completed]').forEach(el => { el.textContent = completed; });
    $$('[data-progress-total]').forEach(el => { el.textContent = total; });

    const timeSpentEl = $('[data-progress-time-spent]');
    if (timeSpentEl) timeSpentEl.textContent = window.LessonProgress.formatTime(window.LessonProgress.getTimeSpent());
}

function closeMobileSidebar() {
    document.querySelector('[data-sidebar]')?.classList.add('-translate-x-full');
    document.querySelector('[data-sidebar-backdrop]')?.classList.add('hidden');
}

function bindSidebarChrome() {
    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        document.querySelector('[data-sidebar]')?.classList.toggle('-translate-x-full');
        document.querySelector('[data-sidebar-backdrop]')?.classList.toggle('hidden');
    });
    document.querySelector('[data-sidebar-backdrop]')?.addEventListener('click', closeMobileSidebar);
    document.querySelector('[data-sidebar-close]')?.addEventListener('click', closeMobileSidebar);

    document.querySelector('[data-lesson-search]')?.addEventListener('input', (e) => {
        const q = e.target.value.trim().toLowerCase();
        $$('[data-lesson-nav]').forEach(item => {
            item.classList.toggle('hidden', q.length > 0 && !item.dataset.lessonName.includes(q));
        });
    });
}

// ---- Bookmarks ---------------------------------------------------------------

const BOOKMARK_ROW_TEMPLATE = `
    <button type="button" data-bookmark-seek class="flex min-w-0 flex-1 items-start gap-2.5 rounded-lg px-1.5 py-1.5 text-left">
        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-xs font-semibold tabular-nums text-(--color-primary)" data-bookmark-time></span>
        <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-semibold text-(--color-text) dark:text-white/90" data-bookmark-title></span>
            <span class="mt-0.5 block truncate text-xs text-(--color-text-secondary)" data-bookmark-note></span>
        </span>
    </button>
    <div class="flex shrink-0 items-center gap-0.5">
        <button type="button" data-bookmark-edit aria-label="Edit bookmark" class="rounded-lg p-1.5 text-(--color-text-secondary) transition-colors hover:bg-black/10 hover:text-(--color-text) dark:hover:bg-white/10">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        </button>
        <button type="button" data-bookmark-delete aria-label="Delete bookmark" class="rounded-lg p-1.5 text-(--color-text-secondary) transition-colors hover:bg-black/10 hover:text-(--color-danger) dark:hover:bg-white/10">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </div>
`;

const BOOKMARK_EDIT_TEMPLATE = `
    <input type="text" data-bookmark-title-input maxlength="60" placeholder="Bookmark title"
           class="input-field !py-1.5 text-sm">
    <textarea data-bookmark-note-input rows="2" maxlength="280" placeholder="Add a short note (optional)"
              class="input-field mt-1.5 resize-none !py-1.5 text-xs"></textarea>
    <div class="mt-1.5 flex justify-end gap-1.5">
        <button type="button" data-bookmark-cancel class="btn-secondary !px-2.5 !py-1 text-xs">Cancel</button>
        <button type="button" data-bookmark-save class="btn-primary !px-2.5 !py-1 text-xs">Save</button>
    </div>
`;

function bookmarkDisplayTitle(bookmark, index) {
    return bookmark.title || `Bookmark ${index + 1}`;
}

function renderBookmarksList() {
    const lessonId = currentLessonId();
    const list = $('[data-bookmarks-list]');
    const empty = $('[data-bookmarks-empty]');
    if (!list || !lessonId) return;

    const bookmarks = window.LessonProgress.getBookmarks(lessonId);
    $$('[data-bookmark-row]', list).forEach(el => el.remove());
    if (empty) empty.classList.toggle('hidden', bookmarks.length > 0);

    bookmarks.forEach((b, index) => {
        const li = document.createElement('li');
        li.dataset.bookmarkRow = '';
        li.className = 'bookmark-row group flex items-start gap-1 rounded-xl px-1 py-1';
        li.innerHTML = BOOKMARK_ROW_TEMPLATE;

        $('[data-bookmark-time]', li).textContent = window.LessonProgress.formatTime(b.time);
        $('[data-bookmark-title]', li).textContent = bookmarkDisplayTitle(b, index);
        const noteEl = $('[data-bookmark-note]', li);
        noteEl.textContent = b.note || '';
        noteEl.classList.toggle('hidden', !b.note);

        $('[data-bookmark-seek]', li).addEventListener('click', () => window.LessonPlayerController.seekTo(b.time));

        $('[data-bookmark-delete]', li).addEventListener('click', (e) => {
            e.stopPropagation();
            window.LessonProgress.removeBookmark(lessonId, b.createdAt);
            renderBookmarksList();
        });

        $('[data-bookmark-edit]', li).addEventListener('click', (e) => {
            e.stopPropagation();
            openBookmarkEditor(li, lessonId, b, index);
        });

        list.appendChild(li);
    });
}

function openBookmarkEditor(row, lessonId, bookmark, index) {
    if ($('[data-bookmark-edit-form]', row)) return; // already open

    const form = document.createElement('div');
    form.dataset.bookmarkEditForm = '';
    form.className = 'mt-1 w-full px-1.5';
    form.innerHTML = BOOKMARK_EDIT_TEMPLATE;
    row.appendChild(form);

    const titleInput = $('[data-bookmark-title-input]', form);
    const noteInput = $('[data-bookmark-note-input]', form);
    titleInput.value = bookmark.title || '';
    noteInput.value = bookmark.note || '';
    titleInput.focus();

    $('[data-bookmark-cancel]', form).addEventListener('click', () => form.remove());
    $('[data-bookmark-save]', form).addEventListener('click', () => {
        window.LessonProgress.updateBookmark(lessonId, bookmark.createdAt, {
            title: titleInput.value,
            note: noteInput.value,
        });
        renderBookmarksList();
    });
}

function addBookmarkAtCurrentTime() {
    const lessonId = currentLessonId();
    if (!lessonId) return;
    const time = window.LessonPlayerController.getCurrentTime();
    window.LessonProgress.addBookmark(lessonId, time);
    renderBookmarksList();
}

function bindBookmarksChrome() {
    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-bookmark-add]')) addBookmarkAtCurrentTime();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'b' && e.key !== 'B') return;
        const tag = (e.target.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea') return;
        addBookmarkAtCurrentTime();
    });
}

// ---- Content tabs (Overview / Bookmarks / Ratings & Reviews) --------------

function bindTabs() {
    const wrapper = $('[data-tabs]');
    if (!wrapper) return;

    const tabs = $$('[data-tab-btn]', wrapper);
    const indicator = $('[data-tab-indicator]', wrapper);

    function activate(name, { focus = false } = {}) {
        tabs.forEach(btn => {
            const active = btn.dataset.tabBtn === name;
            btn.setAttribute('aria-selected', String(active));
            btn.tabIndex = active ? 0 : -1;
            if (active && focus) btn.focus();
            if (active && indicator) {
                indicator.style.width = `${btn.offsetWidth}px`;
                indicator.style.transform = `translateX(${btn.offsetLeft}px)`;
            }
        });
        $$('[data-tab-panel]').forEach(panel => {
            panel.hidden = panel.dataset.tabPanel !== name;
        });
    }

    tabs.forEach((btn, i) => {
        btn.addEventListener('click', () => activate(btn.dataset.tabBtn));
        btn.addEventListener('keydown', (e) => {
            let target = null;
            if (e.key === 'ArrowRight') target = tabs[(i + 1) % tabs.length];
            else if (e.key === 'ArrowLeft') target = tabs[(i - 1 + tabs.length) % tabs.length];
            else if (e.key === 'Home') target = tabs[0];
            else if (e.key === 'End') target = tabs[tabs.length - 1];
            if (!target) return;
            e.preventDefault();
            activate(target.dataset.tabBtn, { focus: true });
        });
    });

    // Overview is always the tab shown first — this whole partial is
    // freshly re-rendered on every lesson swap, so there's no stale state
    // to restore here.
    activate('overview');
}

// ---- Ratings & Reviews ----------------------------------------------------

const STAR_SVG = '<svg viewBox="0 0 24 24" fill="currentColor" class="h-full w-full" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function starsMarkup(rating, sizeClass) {
    return Array.from({ length: 5 }, (_, i) => (
        `<span class="${sizeClass} ${i < rating ? 'text-amber-400' : 'text-(--color-border) dark:text-white/15'}">${STAR_SVG}</span>`
    )).join('');
}

function bindReviewStarInput(root) {
    const buttons = $$('[data-star-input]', root);
    let selected = 0;

    function paint(upTo) {
        buttons.forEach(btn => {
            const filled = Number(btn.dataset.starInput) <= upTo;
            btn.classList.toggle('text-amber-400', filled);
            btn.querySelector('svg')?.classList.toggle('fill-current', filled);
        });
    }

    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', () => paint(Number(btn.dataset.starInput)));
        btn.addEventListener('click', () => {
            selected = Number(btn.dataset.starInput);
            paint(selected);
        });
    });
    root.addEventListener('mouseleave', () => paint(selected));

    return {
        get: () => selected,
        reset: () => { selected = 0; paint(0); },
    };
}

function renderReviewSummary(lessonId) {
    const reviews = window.LessonProgress.getReviews(lessonId);
    const total = reviews.length;
    const average = total ? reviews.reduce((sum, r) => sum + r.rating, 0) / total : 0;

    const averageEl = $('[data-review-average]');
    if (averageEl) averageEl.textContent = average.toFixed(1);

    const starsEl = $('[data-review-average-stars]');
    if (starsEl) starsEl.innerHTML = starsMarkup(Math.round(average), 'h-5 w-5');

    const countEl = $('[data-review-count]');
    if (countEl) countEl.textContent = total;

    for (let star = 1; star <= 5; star++) {
        const count = reviews.filter(r => r.rating === star).length;
        const pct = total ? Math.round((count / total) * 100) : 0;
        const bar = document.querySelector(`[data-review-bar="${star}"]`);
        const pctEl = document.querySelector(`[data-review-bar-pct="${star}"]`);
        if (bar) bar.style.width = `${pct}%`;
        if (pctEl) pctEl.textContent = `${pct}%`;
    }
}

function renderReviewsList(lessonId) {
    const list = $('[data-reviews-list]');
    const empty = $('[data-reviews-empty]');
    if (!list) return;

    const reviews = window.LessonProgress.getReviews(lessonId).slice().sort((a, b) => b.createdAt - a.createdAt);

    $$('[data-review-row]', list).forEach(el => el.remove());
    if (empty) empty.classList.toggle('hidden', reviews.length > 0);

    reviews.forEach(r => {
        const card = document.createElement('div');
        card.dataset.reviewRow = '';
        card.className = 'lesson-card p-4 sm:p-5';
        card.innerHTML = `
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-xs font-semibold text-white">${escapeHtml((r.name || 'U').charAt(0).toUpperCase())}</span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                        <span class="text-sm font-bold text-(--color-text) dark:text-white">${escapeHtml(r.name || 'Anonymous')}</span>
                        <span class="text-xs text-(--color-text-secondary)">${escapeHtml(new Date(r.createdAt).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }))}</span>
                    </div>
                    <div class="mt-1 flex items-center gap-0.5">${starsMarkup(r.rating, 'h-3.5 w-3.5')}</div>
                    ${r.text ? `<p class="mt-2 text-sm leading-relaxed text-(--color-text-secondary)">${escapeHtml(r.text)}</p>` : ''}
                </div>
            </div>
        `;
        list.appendChild(card);
    });
}

function bindReviewForm(lessonId, starController) {
    const submitBtn = $('[data-review-submit]');
    const textField = $('[data-review-text]');
    const errorEl = $('[data-review-error]');
    if (!submitBtn) return;

    submitBtn.addEventListener('click', () => {
        const rating = starController.get();
        if (!rating) {
            if (errorEl) errorEl.textContent = 'Please select a star rating.';
            return;
        }
        if (errorEl) errorEl.textContent = '';

        window.LessonProgress.addReview(lessonId, {
            rating,
            text: textField ? textField.value : '',
            name: window.__userName,
        });

        if (textField) textField.value = '';
        starController.reset();

        renderReviewSummary(lessonId);
        renderReviewsList(lessonId);
    });
}

function bindReviews() {
    const lessonId = currentLessonId();
    if (!lessonId) return;

    renderReviewSummary(lessonId);
    renderReviewsList(lessonId);

    const inputRoot = $('[data-review-star-input]');
    const starController = inputRoot ? bindReviewStarInput(inputRoot) : { get: () => 0, reset: () => {} };
    bindReviewForm(lessonId, starController);
}

// ---- Lesson navigation (AJAX swap, player instance preserved) -----------------

function initLessonContentView() {
    bindTabs();
    renderBookmarksList();
    bindReviews();
    paintProgress();
    paintCompleted();
}

async function fetchLessonHtml(url) {
    if (prefetchCache.has(url)) {
        const html = prefetchCache.get(url);
        prefetchCache.delete(url);
        return html;
    }
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    return res.text();
}

function prefetchNext() {
    const nextLink = document.querySelector('[data-lesson-nav][data-next-lesson]');
    if (!nextLink || prefetchCache.has(nextLink.href)) return;
    const idle = window.requestIdleCallback || ((fn) => setTimeout(fn, 300));
    idle(() => {
        fetch(nextLink.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => prefetchCache.set(nextLink.href, html))
            .catch(() => {});
    });
}

async function loadLesson(url, lessonId, push) {
    const content = document.getElementById('lessonContent');
    content.style.opacity = '0.5';

    try {
        const html = await fetchLessonHtml(url);
        const doc = new DOMParser().parseFromString(html, 'text/html');

        const newPlayer = doc.querySelector('#playerRegion media-player');
        const newContent = doc.getElementById('lessonContent');
        if (!newPlayer || !newContent) { window.location.href = url; return; }

        window.LessonPlayerController.updateLesson({
            src: newPlayer.dataset.src,
            title: newPlayer.getAttribute('title') || '',
            lessonId: newPlayer.dataset.lessonId,
            courseId: newPlayer.dataset.courseId,
            nextUrl: newPlayer.dataset.nextUrl,
            nextId: newPlayer.dataset.nextId,
            nextTitle: newPlayer.dataset.nextTitle,
        });

        content.innerHTML = newContent.innerHTML;
        document.title = doc.title;

        activateSidebarItem(lessonId);
        initLessonContentView();

        if (push) history.pushState({ lessonId }, '', url);
        prefetchNext();
    } catch (e) {
        window.location.href = url;
    } finally {
        content.style.opacity = '1';
    }
}

function bindNavigation() {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('[data-lesson-nav]');
        if (!link) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
        e.preventDefault();
        loadLesson(link.href, link.dataset.lessonId, true);
    });

    window.addEventListener('popstate', (e) => {
        loadLesson(window.location.href, e.state ? e.state.lessonId : null, false);
    });

    document.addEventListener('lesson:auto-advance', (e) => {
        loadLesson(e.detail.url, e.detail.lessonId, true);
    });
}

function bindProgressEvents() {
    document.addEventListener('lesson:duration-known', paintDurations);
    document.addEventListener('lesson:completed', () => {
        paintProgress();
        paintCompleted();
    });
}

// ---- Boot ---------------------------------------------------------------

function boot() {
    const playerRegion = document.getElementById('playerRegion');
    if (playerRegion) window.LessonPlayerController.init(playerRegion);

    bindSidebarChrome();
    bindNavigation();
    bindBookmarksChrome();
    bindProgressEvents();

    if (currentLessonId()) {
        activateSidebarItem(currentLessonId());
        initLessonContentView();
        prefetchNext();
    }
    paintDurations();
}

boot();
