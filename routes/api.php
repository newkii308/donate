<?php

use App\Http\Controllers\Api\OverlayController;
use App\Http\Controllers\Api\DonationGoalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Overlay polling API
|--------------------------------------------------------------------------
|
| The OBS overlay polls these endpoints every few seconds instead of using
| WebSockets, keeping the platform compatible with shared hosting. The
| overlay_key acts as the secret that identifies the streamer.
|
*/

Route::middleware('throttle:120,1')->group(function () {
    Route::get('/overlay/{streamer:overlay_key}/check', [OverlayController::class, 'check'])->name('api.overlay.check');
    Route::post('/overlay/{streamer:overlay_key}/complete', [OverlayController::class, 'complete'])->name('api.overlay.complete');
    
    // Donation Goal Widget polling status
    Route::get('/widget/goal/{streamer:overlay_key}/check', [DonationGoalController::class, 'check'])->name('api.widget.goal.check');
});
