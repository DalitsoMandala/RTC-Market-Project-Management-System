<?php

use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;





Schedule::command('send:expired-period-notifications')->dailyAt('00:00')->evenInMaintenanceMode();

Schedule::command('check:submission-deadlines')->dailyAt('00:00')->evenInMaintenanceMode();

Schedule::command('update:information')->hourly()->evenInMaintenanceMode();

Schedule::command('clean-db')->dailyAt('01:00')->onFailure(function () {
    Log::error('Backup clean-up failed');
})->onSuccess(function () {
    Log::info("Backup clean-up completed");
})->evenInMaintenanceMode();
Schedule::command('backup-db')->dailyAt('01:30')->onFailure(function () {
    Log::error('Backup failed');
})->onSuccess(function () {
    Log::info("Backup completed");
})->evenInMaintenanceMode();

Schedule::command('schedule:test')->everyMinute()->evenInMaintenanceMode();
