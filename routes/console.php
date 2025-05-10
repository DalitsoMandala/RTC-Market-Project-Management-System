<?php

use Illuminate\Support\Stringable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;



Schedule::command('update:information')->dailyAt('00:00');

Schedule::command('send:expired-period-notifications')->dailyAt('00:00');

Schedule::command('check:submission-deadlines')->dailyAt('00:00');


// Schedule::command('send:expired-period-notifications')->everyThirtySeconds(); // testing
// sleep(10);
// Schedule::command('check:submission-deadlines')->everyThirtySeconds(); // testing
