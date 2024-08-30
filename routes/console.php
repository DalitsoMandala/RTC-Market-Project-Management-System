<?php

use Illuminate\Support\Stringable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchExchangeRates;



Schedule::command('exchange-rates:fetch')->daily()->at('06:00')->onFailure(function (Stringable $output) {
    // dd($output);
});
