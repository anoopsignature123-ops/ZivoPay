<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Cron Schedule Registrations
|--------------------------------------------------------------------------
| Daily ROI Yield Distribution & Automatic Income Calculations
| Runs every night at midnight (00:00) automatically without overlapping.
*/
Schedule::command('roi:distribute')->dailyAt('00:00')->withoutOverlapping();
Schedule::command('income:process')->dailyAt('00:05')->withoutOverlapping();
Schedule::command('deposits:check-pending')->everyFiveMinutes()->withoutOverlapping();
