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

        if (name === 'assets') loadAssetsPanel();
        if (name === 'reviews') loadReviewsPanel();
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

// ---- Course-scoped tab panels (Assets, Ratings & Reviews) -----------------
// Both are scoped to the course, not the lesson, so they're fetched once per
// course and cached in memory here — switching lessons repaints instantly
// from the cache (via bindTabs()'s activate()) instead of re-fetching data
// that hasn't changed.

const panelCache = new Map(); // `${name}:${courseId}` -> rendered HTML
const panelPending = new Set(); // same keys, marks an in-flight fetch

function panelKey(name) {
    return `${name}:${currentCourseId()}`;
}

function loadReviewsPanel() {
    const mount = $('[data-reviews-mount]');
    if (!mount) return;

    const key = panelKey('reviews');
    if (panelCache.has(key)) {
        mount.innerHTML = panelCache.get(key);
        bindReviewsPanel(mount);
        return;
    }
    if (panelPending.has(key)) return;
    panelPending.add(key);

    window.axios.get(`/courses/${window.__courseSlug}/reviews`)
        .then(res => {
            panelCache.set(key, res.data);
            const freshMount = $('[data-reviews-mount]');
            if (freshMount) {
                freshMount.innerHTML = res.data;
                bindReviewsPanel(freshMount);
            }
        })
        .catch(() => {
            const freshMount = $('[data-reviews-mount]');
            if (freshMount) freshMount.innerHTML = '<div class="lesson-card p-6 text-center text-sm text-(--color-danger)">Couldn’t load reviews. Please try again.</div>';
        })
        .finally(() => panelPending.delete(key));
}

function bindReviewsPanel(mount) {
    const starRoot = $('[data-review-star-input]', mount);
    const submitBtn = $('[data-review-submit]', mount);
    const deleteBtn = $('[data-review-delete]', mount);
    const textField = $('[data-review-text]', mount);
    const errorEl = $('[data-review-error]', mount);

    let selected = submitBtn ? Number(submitBtn.dataset.initialRating || 0) : 0;

    function paintStars(upTo) {
        if (!starRoot) return;
        $$('[data-star-input]', starRoot).forEach(btn => {
            const filled = Number(btn.dataset.starInput) <= upTo;
            btn.classList.toggle('text-amber-400', filled);
            btn.querySelector('svg')?.classList.toggle('fill-current', filled);
        });
    }

    if (starRoot) {
        $$('[data-star-input]', starRoot).forEach(btn => {
            btn.addEventListener('mouseenter', () => paintStars(Number(btn.dataset.starInput)));
            btn.addEventListener('click', () => {
                selected = Number(btn.dataset.starInput);
                paintStars(selected);
            });
        });
        starRoot.addEventListener('mouseleave', () => paintStars(selected));
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            const body = textField ? textField.value.trim() : '';
            if (!selected) {
                if (errorEl) errorEl.textContent = 'Please select a star rating.';
                return;
            }
            if (body.length < 3) {
                if (errorEl) errorEl.textContent = 'Please write a few words about the course.';
                return;
            }
            if (errorEl) errorEl.textContent = '';
            submitBtn.disabled = true;

            window.axios.post(`/courses/${window.__courseSlug}/reviews`, { rating: selected, body })
                .then(res => repaintReviewsPanel(res.data))
                .catch(err => {
                    if (errorEl) errorEl.textContent = err.response?.data?.message || 'Something went wrong. Please try again.';
                })
                .finally(() => { submitBtn.disabled = false; });
        });
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            if (!window.confirm('Delete your review?')) return;
            deleteBtn.disabled = true;
            window.axios.delete(`/courses/${window.__courseSlug}/reviews`)
                .then(res => repaintReviewsPanel(res.data))
                .catch(() => { deleteBtn.disabled = false; });
        });
    }

    bindReviewsLoadMore(mount);
}

function repaintReviewsPanel(html) {
    panelCache.set(panelKey('reviews'), html);
    const mount = $('[data-reviews-mount]');
    if (mount) {
        mount.innerHTML = html;
        bindReviewsPanel(mount);
    }
}

function bindReviewsLoadMore(mount) {
    const btn = mount.querySelector('[data-reviews-load-more]');
    if (!btn || btn.dataset.bound) return;
    btn.dataset.bound = '1';

    btn.addEventListener('click', () => {
        const page = Number(btn.dataset.page);
        btn.disabled = true;
        btn.textContent = 'Loading…';

        window.axios.get(`/courses/${window.__courseSlug}/reviews/more`, { params: { page } })
            .then(res => {
                const temp = document.createElement('div');
                temp.innerHTML = res.data;
                const newButton = temp.querySelector('[data-reviews-load-more]');
                if (newButton) newButton.remove();

                const cards = mount.querySelector('[data-reviews-cards]');
                if (cards) Array.from(temp.children).forEach(card => cards.appendChild(card));

                const wrapper = mount.querySelector('[data-reviews-load-more-wrapper]');
                if (wrapper) {
                    wrapper.innerHTML = '';
                    if (newButton) {
                        wrapper.appendChild(newButton);
                        bindReviewsLoadMore(mount);
                    }
                }

                panelCache.set(panelKey('reviews'), mount.innerHTML);
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Load more reviews';
            });
    });
}

// ---- Assets -----------------------------------------------------------

function loadAssetsPanel() {
    const mount = $('[data-assets-mount]');
    if (!mount) return;

    const key = panelKey('assets');
    if (panelCache.has(key)) {
        mount.innerHTML = panelCache.get(key);
        bindAssetsPanel(mount);
        return;
    }
    if (panelPending.has(key)) return;
    panelPending.add(key);

    window.axios.get(`/courses/${window.__courseSlug}/assets`)
        .then(res => {
            panelCache.set(key, res.data);
            const freshMount = $('[data-assets-mount]');
            if (freshMount) {
                freshMount.innerHTML = res.data;
                bindAssetsPanel(freshMount);
            }
        })
        .catch(() => {
            const freshMount = $('[data-assets-mount]');
            if (freshMount) freshMount.innerHTML = '<div class="lesson-card p-6 text-center text-sm text-(--color-danger)">Couldn’t load resources. Please try again.</div>';
        })
        .finally(() => panelPending.delete(key));
}

function bindAssetsPanel(mount) {
    bindAssetsLoadMore(mount);
}

function bindAssetsLoadMore(mount) {
    const btn = mount.querySelector('[data-assets-load-more]');
    if (!btn || btn.dataset.bound) return;
    btn.dataset.bound = '1';

    btn.addEventListener('click', () => {
        const page = Number(btn.dataset.page);
        btn.disabled = true;
        btn.textContent = 'Loading…';

        window.axios.get(`/courses/${window.__courseSlug}/assets/more`, { params: { page } })
            .then(res => {
                const temp = document.createElement('div');
                temp.innerHTML = res.data;
                const newButton = temp.querySelector('[data-assets-load-more]');
                if (newButton) newButton.remove();

                const cards = mount.querySelector('[data-assets-cards]');
                if (cards) Array.from(temp.children).forEach(card => cards.appendChild(card));

                const wrapper = mount.querySelector('[data-assets-load-more-wrapper]');
                if (wrapper) {
                    wrapper.innerHTML = '';
                    if (newButton) {
                        wrapper.appendChild(newButton);
                        bindAssetsLoadMore(mount);
                    }
                }

                panelCache.set(panelKey('assets'), mount.innerHTML);
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Load more';
            });
    });
}

// ---- Lesson navigation (AJAX swap, player instance preserved) -----------------

function initLessonContentView() {
    bindTabs();
    renderBookmarksList();
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
