<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule currency rate updates twice daily (8:00 AM and 6:00 PM)
Schedule::command('currency:update-rates')->dailyAt('08:00');
Schedule::command('currency:update-rates')->dailyAt('18:00');
