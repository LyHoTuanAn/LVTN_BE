<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync movie status every minute based on showtimes
Schedule::command('movies:sync-status')
    ->everyMinute()
    ->description('Sync movie status based on showtimes (COMING_SOON, UPCOMING, NOW_SHOWING)')
    ->withoutOverlapping();

Schedule::call(function () {
    file_put_contents(
        storage_path('logs/cron_test.log'),
        now() . " - cron OK\n",
        FILE_APPEND
    );
})->everyMinute();
