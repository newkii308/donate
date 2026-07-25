<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shared-hosting Cron: point a single cron entry at `php artisan schedule:run`
// (every minute) and Laravel will trigger the cleanup hourly.
Schedule::command('newlab:cleanup')->hourly();
