<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync movie status every minute based on release_date
Schedule::command('movies:sync-status')
    ->everyMinute()
    ->description('Sync movie status based on release_date (COMING_SOON, NOW_SHOWING)')
    ->withoutOverlapping();

// Sync showtime status every minute based on current time and auto-update booking status
Schedule::command('showtimes:sync-status')
    ->everyMinute()
    ->description('Sync showtime status based on current time (scheduled, ongoing, completed) and auto-update booking status when showtime completed')
    ->withoutOverlapping();

Schedule::call(function () {
    file_put_contents(
        storage_path('logs/cron_test.log'),
        now() . " - cron OK\n",
        FILE_APPEND
    );
})->everyMinute();
