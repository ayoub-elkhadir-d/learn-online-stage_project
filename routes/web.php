<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseAssetController;
use App\Http\Controllers\CourseReviewController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\UserController;

// Fallback for the "public" disk's Storage::url() links — see
// app/Http/Controllers/StorageController.php for why this exists
// alongside the storage:link symlink instead of replacing it.
Route::get('/storage/{path}', [StorageController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.show');

// Language switcher — available to guests and authenticated users alike,
// on every page (see resources/views/components/language-switcher.blade.php).
// POST-only so it's a real state change, not a link a crawler/prefetch
// could trigger.
Route::post('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Public course catalog — blocked for admins. The Courses page is also the
// public landing page, so "/" renders the exact same controller action/view.
Route::middleware('user.only')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('home');
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
});

// Authenticated users only (not admins)
Route::middleware(['auth', 'user.only'])->group(function () {
    // {course:slug} (implicit binding by the slug column) is required here —
    // 'course.purchased' reads $request->route('course') expecting an
    // already-resolved Course model, and denies access unless the viewer
    // has a CoursePurchase with status === 'paid' for it (see
    // App\Http\Middleware\EnsureCoursePurchased / App\Policies\CoursePolicy).
    // Rejected/cancelled purchases fail this the same as never having
    // purchased at all.
    Route::get('/courses/{course:slug}/learn', [CourseController::class, 'learn'])
        ->middleware('course.purchased')
        ->name('courses.learn');
    Route::get('/lessons/{lesson}/video', [VideoController::class, 'stream'])
        // A single <video> element issues many Range requests per lesson
        // (buffer refills, seeking), especially on mobile where buffering
        // is more conservative — 120/min was getting hit mid-playback on
        // long videos and looked like stalling/failed loads.
        ->middleware('throttle:600,1')
        ->name('lessons.video');
    Route::get('/courses/{slug}/checkout', [CourseController::class, 'checkout'])->name('courses.checkout');
    Route::post('/courses/{slug}/purchase', [PurchaseController::class, 'purchase'])->name('courses.purchase');

    // Ratings & Reviews — read/write panel embedded in the Learning page's
    // "Ratings & Reviews" tab, fetched/mutated via AJAX (HTML fragments).
    Route::get('/courses/{course:slug}/reviews', [CourseReviewController::class, 'index'])->name('courses.reviews.index');
    Route::get('/courses/{course:slug}/reviews/more', [CourseReviewController::class, 'loadMore'])->name('courses.reviews.more');
    Route::post('/courses/{course:slug}/reviews', [CourseReviewController::class, 'store'])->name('courses.reviews.store');
    Route::delete('/courses/{course:slug}/reviews', [CourseReviewController::class, 'destroy'])->name('courses.reviews.destroy');

    // Assets — read-only instructor resource timeline in the Learning
    // page's "Assets" tab. No write endpoints yet (admin panel comes later).
    Route::get('/courses/{course:slug}/assets', [CourseAssetController::class, 'index'])->name('courses.assets.index');
    Route::get('/courses/{course:slug}/assets/more', [CourseAssetController::class, 'loadMore'])->name('courses.assets.more');

    Route::get('/dashboard', function () {
        // Rejected/cancelled purchases are kept for admin history/auditing
        // but must never show up as "My Courses" — only an active (paid) or
        // still-being-reviewed (pending) purchase belongs here.
        $purchases = \App\Models\CoursePurchase::with(['course.category'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'paid'])
            ->latest()
            ->paginate(9);
        return view('dashboard', compact('purchases'));
    })->name('dashboard');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// Shared logout (both roles)
Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('courses', AdminCourseController::class);
    Route::resource('courses.lessons', LessonController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Must be registered before /payments/{purchase} — otherwise "bank-settings"
    // would be matched as a {purchase} route parameter.
    Route::get('/payments/bank-settings', [PaymentSettingController::class, 'edit'])->name('payments.bank-settings.edit');
    Route::put('/payments/bank-settings', [PaymentSettingController::class, 'update'])->name('payments.bank-settings.update');

    Route::get('/payments/{purchase}', [PaymentController::class, 'show'])->name('payments.show');
    Route::put('/payments/{purchase}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::put('/payments/{purchase}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::put('/payments/{purchase}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::put('/users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
});
