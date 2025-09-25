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
// Schedule::command('send:expired-period-notifications')->everyThirtySeconds(); // testing
// sleep(10);
// Schedule::command('check:submission-deadlines')->everyThirtySeconds(); // testing


//Backup
Schedule::command('backup:clean')->daily()->at('01:00')->onFailure(function () {
    Log::error('Backup failed');
})->onSuccess(function () {
    Log::info("Backup completed");
});
Schedule::command('backup:run --only-db')->daily()->at('01:30')->onFailure(function () {
    Log::error('Backup failed');
})->onSuccess(function () {
    Log::info("Backup completed");
});
