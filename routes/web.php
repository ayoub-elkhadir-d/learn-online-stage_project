<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\LessonController;

Route::get('/', function () {
    return view('welcome');
});

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

// Public course catalog — blocked for admins
Route::middleware('user.only')->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
});

// Authenticated users only (not admins)
Route::middleware(['auth', 'user.only'])->group(function () {
    Route::get('/courses/{slug}/learn', [CourseController::class, 'learn'])->name('courses.learn');

    Route::prefix('lessons/{lesson}/hls')->name('lessons.hls.')->group(function () {
        Route::get('bootstrap', [VideoStreamController::class, 'bootstrap'])
            ->middleware('throttle:60,1')->name('bootstrap');
        Route::get('playlist.m3u8', [VideoStreamController::class, 'playlist'])
            ->middleware(['signed', 'throttle:300,1'])->name('playlist');
        Route::get('segments/{segment}', [VideoStreamController::class, 'segment'])
            ->where('segment', '[A-Za-z0-9_\-]+\.ts')
            ->middleware(['signed', 'throttle:1200,1'])->name('segment');
    });

    // Dedicated AES-128 key delivery endpoint. Kept outside the lessons/hls
    // prefix at its own path per spec — still requires auth + user.only
    // (this group) plus a short-lived signed signature (below), and the
    // controller re-checks course purchase on every hit.
    Route::get('/video/key/{lesson}', [VideoStreamController::class, 'key'])
        ->middleware(['signed', 'throttle:30,1'])->name('video.key');

    Route::get('/courses/{slug}/checkout', [CourseController::class, 'checkout'])->name('courses.checkout');
    Route::post('/courses/{slug}/purchase', [PurchaseController::class, 'purchase'])->name('courses.purchase');

    Route::get('/dashboard', function () {
        $purchases = \App\Models\CoursePurchase::with(['course.category'])
            ->where('user_id', auth()->id())
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
    Route::resource('courses', AdminCourseController::class);
    Route::resource('courses.lessons', LessonController::class)->except(['show']);

    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::put('/payments/{purchase}/approve', [\App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
    Route::delete('/payments/{purchase}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
    Route::put('/users/{user}/unban', [\App\Http\Controllers\Admin\UserController::class, 'unban'])->name('users.unban');
});
