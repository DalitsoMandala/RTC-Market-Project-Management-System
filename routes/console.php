<?php

use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;





Schedule::command('send:expired-period-notifications')->dailyAt('00:00');

Schedule::command('check:submission-deadlines')->dailyAt('00:00');

Schedule::command('update:information')->hourly();

Schedule::command('clean-db')->dailyAt('01:00')->onFailure(function () {
    Log::error('Backup clean-up failed');
})->onSuccess(function () {
    Log::info("Backup clean-up completed");
});
Schedule::command('backup-db')->dailyAt('01:30')->onFailure(function () {
    Log::error('Backup failed');
})->onSuccess(function () {
    Log::info("Backup completed");
});

Schedule::command('schedule:test')->everyTenSeconds();
