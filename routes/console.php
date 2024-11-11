<?php

use Illuminate\Support\Stringable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;



//Schedule::command('update:information')->dailyAt('00:01');
Schedule::command('update:information')->dailyAt('00:01');
Schedule::command('notifications:send-expired-periods')->dailyAt('00:01');
Schedule::call(function () {
    $files = glob(public_path('exports/*')); // Get all files in the exports directory

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // Delete each file
        }
    }
})->daily()->at('00:00');
