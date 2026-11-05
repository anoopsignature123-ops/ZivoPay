<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('roi:distribute')->everyMinute()->withoutOverlapping();
Schedule::command('income:process')->dailyAt('00:05')->withoutOverlapping();
Schedule::command('deposits:check-pending')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('recharges:sync-pending --limit=25')->everyTwoMinutes()->withoutOverlapping(5);
