/**
 * localStorage-backed learning progress. Deliberately client-side only (no
 * backend changes) — everything here persists per-browser, not per-account
 * across devices. Exposed as window.LessonProgress since both player.js and
 * learn-page.js need it.
 */
(function () {
    'use strict';

    function read(key, fallback) {
        try {
            const raw = localStorage.getItem(key);
            return raw === null ? fallback : JSON.parse(raw);
        } catch (e) {
            return fallback;
        }
    }

    function write(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) { /* storage unavailable/full — fail silently */ }
    }

    const LessonProgress = {
        // ---- Completion (scoped per course so a % can be computed) ----
        isCompleted(courseId, lessonId) {
            return read(`lp:completed:${courseId}`, []).includes(lessonId);
        },
        markCompleted(courseId, lessonId) {
            const set = new Set(read(`lp:completed:${courseId}`, []));
            set.add(lessonId);
            write(`lp:completed:${courseId}`, Array.from(set));
        },
        completedCount(courseId) {
            return read(`lp:completed:${courseId}`, []).length;
        },

        // ---- Resume position ----
        getPosition(lessonId) {
            return read(`lp:position:${lessonId}`, 0);
        },
        savePosition(lessonId, seconds) {
            write(`lp:position:${lessonId}`, Math.max(0, Math.floor(seconds)));
        },
        clearPosition(lessonId) {
            localStorage.removeItem(`lp:position:${lessonId}`);
        },

        // ---- Duration cache (no server-side duration data available) ----
        getDuration(lessonId) {
            return read(`lp:duration:${lessonId}`, null);
        },
        saveDuration(lessonId, seconds) {
            if (seconds && isFinite(seconds)) write(`lp:duration:${lessonId}`, Math.floor(seconds));
        },

        // ---- Player preferences (global, not per-lesson) ----
        getVolume() {
            return read('lp:volume', 1);
        },
        saveVolume(v) {
            write('lp:volume', v);
        },
        getMuted() {
            return read('lp:muted', false);
        },
        saveMuted(v) {
            write('lp:muted', v);
        },
        getSpeed() {
            return read('lp:speed', 1);
        },
        saveSpeed(v) {
            write('lp:speed', v);
        },

        // ---- Notes (per lesson, debounced-save from the caller) ----
        getNote(lessonId) {
            return read(`lp:note:${lessonId}`, '');
        },
        saveNote(lessonId, text) {
            write(`lp:note:${lessonId}`, text);
        },

        // ---- Bookmarks (timestamped markers per lesson) ----
        // Shape: { time, title, note, createdAt }. Older records only ever had
        // `label` — normalized to `title` here on read so nothing already
        // saved in a user's browser gets silently dropped by the rename.
        getBookmarks(lessonId) {
            return read(`lp:bookmarks:${lessonId}`, []).map(b => ({
                time: b.time,
                title: (b.title ?? b.label ?? '').trim(),
                note: b.note ?? '',
                createdAt: b.createdAt,
            }));
        },
        addBookmark(lessonId, time, title) {
            const list = this.getBookmarks(lessonId);
            list.push({ time: Math.floor(time), title: (title || '').trim(), note: '', createdAt: Date.now() });
            list.sort((a, b) => a.time - b.time);
            write(`lp:bookmarks:${lessonId}`, list);
            return list;
        },
        updateBookmark(lessonId, createdAt, { title, note } = {}) {
            const list = this.getBookmarks(lessonId).map(b => {
                if (b.createdAt !== createdAt) return b;
                return {
                    ...b,
                    title: title !== undefined ? title.trim() : b.title,
                    note: note !== undefined ? note : b.note,
                };
            });
            write(`lp:bookmarks:${lessonId}`, list);
            return list;
        },
        removeBookmark(lessonId, createdAt) {
            const list = this.getBookmarks(lessonId).filter(b => b.createdAt !== createdAt);
            write(`lp:bookmarks:${lessonId}`, list);
            return list;
        },

        // ---- Time spent learning (running total, seconds) ----
        addTimeSpent(seconds) {
            const total = read('lp:time-spent', 0) + Math.max(0, seconds);
            write('lp:time-spent', total);
            return total;
        },
        getTimeSpent() {
            return read('lp:time-spent', 0);
        },

        // ---- Streak (consecutive distinct days with activity) ----
        recordActivity() {
            const today = new Date().toISOString().slice(0, 10);
            const dates = read('lp:streak-dates', []);
            if (dates[dates.length - 1] !== today) {
                dates.push(today);
                write('lp:streak-dates', dates.slice(-60)); // cap growth
            }
        },
        getStreak() {
            const dates = read('lp:streak-dates', []);
            if (dates.length === 0) return 0;

            let streak = 1;
            const oneDay = 86400000;
            for (let i = dates.length - 1; i > 0; i--) {
                const diff = new Date(dates[i]) - new Date(dates[i - 1]);
                if (diff === oneDay) streak++;
                else break;
            }

            const lastActive = new Date(dates[dates.length - 1]);
            const daysSinceActive = Math.round((Date.now() - lastActive) / oneDay);
            return daysSinceActive > 1 ? 0 : streak;
        },

        // ---- Continue learning pointer (read by the Dashboard) ----
        setContinueLearning(url, title) {
            write('continue-learning', { url, title, timestamp: Date.now() });
        },

        // ---- Helpers ----
        formatTime(totalSeconds) {
            totalSeconds = Math.max(0, Math.floor(totalSeconds || 0));
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            const pad = n => String(n).padStart(2, '0');
            return h > 0 ? `${h}:${pad(m)}:${pad(s)}` : `${m}:${pad(s)}`;
        },
    };

    window.LessonProgress = LessonProgress;
})();
