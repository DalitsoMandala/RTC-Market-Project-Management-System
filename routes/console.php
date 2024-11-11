<?php

use Illuminate\Support\Stringable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;



//Schedule::command('update:information')->dailyAt('00:01');
Schedule::command('update:information')->dailyAt('00:01');
Schedule::command('notifications:send-expired-periods')->dailyAt('00:01');
