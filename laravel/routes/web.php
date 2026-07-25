<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationGoalController;
use App\Http\Controllers\OverlayController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Streamer;
use App\Http\Controllers\Streamer\DonationGoalController as StreamerDonationGoalController;
use App\Http\Controllers\TtsController;
use App\Models\ContentBlock;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome', [
        'blocks' => ContentBlock::forPage('welcome')->visible()->orderBy('sort_order')->get(),
        'news' => ContentBlock::forPage('news')->visible()->latest('updated_at')->limit(3)->get(),
        'communities' => ContentBlock::forPage('community')->visible()->orderBy('sort_order')->limit(3)->get(),
    ]);
})->middleware('site.up')->name('home');

// Public information portal managed by the administrator.
Route::get('/{page}', [PageController::class, 'show'])
    ->whereIn('page', ['news', 'community', 'about', 'faq', 'terms', 'privacy', 'rules', 'withdrawal-terms', 'contact'])
    ->middleware('site.up')
    ->name('page.show');

// Public donation page — /donate/{username}
Route::get('/donate/{streamer:username}', [DonationController::class, 'show'])->middleware('site.up')->name('donate.show');
Route::post('/donate/{streamer:username}', [DonationController::class, 'store'])
    ->middleware(['site.up', 'throttle:15,1'])
    ->name('donate.store');

// OBS overlay (browser source) — /overlay/{overlay_key}
Route::get('/overlay/{streamer:overlay_key}', [OverlayController::class, 'show'])->name('overlay.show');

// OBS donation goal widget — /widget/goal/{overlay_key}
Route::get('/widget/goal/{streamer:overlay_key}', [DonationGoalController::class, 'show'])->name('widget.goal.show');

// พร็อกซีเสียงไทยฝั่งเซิร์ฟเวอร์ (ใช้เมื่อเครื่องผู้ใช้ไม่มีเสียงไทย) — /tts?q=...
Route::get('/tts', [TtsController::class, 'speak'])
    ->middleware('throttle:120,1')
    ->name('tts.speak');

/*
|--------------------------------------------------------------------------
| Guest authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Streamer dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:streamer'])->prefix('dashboard')->group(function () {
    Route::get('/', [Streamer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [Streamer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Streamer\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/donation-page', [Streamer\DonationPageController::class, 'edit'])->name('donation-page.edit');
    Route::put('/donation-page', [Streamer\DonationPageController::class, 'update'])->name('donation-page.update');

    Route::get('/overlay', [Streamer\OverlaySettingController::class, 'edit'])->name('overlay-settings.edit');
    Route::put('/overlay', [Streamer\OverlaySettingController::class, 'update'])->name('overlay-settings.update');
    Route::post('/overlay/regenerate-key', [Streamer\OverlaySettingController::class, 'regenerateKey'])->name('overlay-settings.regenerate');

    Route::get('/donation-goal', [StreamerDonationGoalController::class, 'edit'])->name('donation-goal.edit');
    Route::put('/donation-goal', [StreamerDonationGoalController::class, 'update'])->name('donation-goal.update');
    Route::post('/donation-goal/test-update', [StreamerDonationGoalController::class, 'testUpdate'])->name('donation-goal.test-update');

    Route::get('/media', [Streamer\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [Streamer\MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{medium}', [Streamer\MediaController::class, 'destroy'])->name('media.destroy');

    Route::get('/donations', [Streamer\DonationHistoryController::class, 'index'])->name('donations.index');
    Route::get('/donations/export', [Streamer\DonationHistoryController::class, 'export'])->name('donations.export');

    Route::get('/wallet', [Streamer\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/withdraw', [Streamer\WalletController::class, 'store'])->name('wallet.withdraw');
    Route::patch('/wallet/withdrawals/{withdrawal}/cancel', [Streamer\WalletController::class, 'cancel'])->name('wallet.cancel');

    Route::post('/test-notification', [Streamer\TestNotificationController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('test-notification.store');
});

/*
|--------------------------------------------------------------------------
| Administrator
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle', [Admin\UserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/streamers', [Admin\StreamerController::class, 'index'])->name('streamers.index');
    Route::get('/streamers/{streamer}', [Admin\StreamerController::class, 'show'])->name('streamers.show');
    Route::patch('/streamers/{streamer}/toggle', [Admin\StreamerController::class, 'toggleActive'])->name('streamers.toggle');

    Route::get('/donations', [Admin\DonationController::class, 'index'])->name('donations.index');
    Route::patch('/donations/{donation}/verify', [Admin\DonationController::class, 'verify'])->name('donations.verify');
    Route::patch('/donations/{donation}/reject', [Admin\DonationController::class, 'reject'])->name('donations.reject');

    Route::get('/withdrawals', [Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::patch('/withdrawals/{withdrawal}/approve', [Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::patch('/withdrawals/{withdrawal}/paid', [Admin\WithdrawalController::class, 'paid'])->name('withdrawals.paid');
    Route::patch('/withdrawals/{withdrawal}/reject', [Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    Route::get('/media', [Admin\MediaController::class, 'index'])->name('media.index');
    Route::delete('/media/{medium}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');

    Route::get('/logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index');

    Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    // จัดการเนื้อหาหน้าเว็บ (Landing/About/Terms/Privacy/FAQ)
    Route::get('/content', [Admin\ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{page}', [Admin\ContentController::class, 'edit'])->name('content.edit');
    Route::post('/content', [Admin\ContentController::class, 'store'])->name('content.store');
    Route::put('/content/{block}', [Admin\ContentController::class, 'update'])->name('content.update');
    Route::delete('/content/{block}', [Admin\ContentController::class, 'destroy'])->name('content.destroy');
});
