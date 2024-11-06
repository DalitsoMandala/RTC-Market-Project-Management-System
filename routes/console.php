<?php

use Illuminate\Support\Stringable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;



Schedule::command('update:information')->dailyAt('05:00');
