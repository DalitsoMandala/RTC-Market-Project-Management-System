<?php

namespace App\Console\Commands;

use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchExchangeRates extends Command
{
    protected $signature = 'exchange-rates:fetch';

    protected $description = 'Fetch exchange rates and save them to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $apiKey = env('EXCHANGE_RATE_API'); // Store your API key in config/services.php
        $baseCurrency = 'USD'; // or any other base currency
        $url = "v6.exchangerate-api.com/v6/{$apiKey}/latest/{$baseCurrency}";

        $response = Http::get($url);

        if ($response->successful()) {
            $rates = $response->json()['conversion_rates'];


            $date = now()->toDateString();

            foreach ($rates as $currency => $rate) {

                if ($currency === 'MWK') {
                    ExchangeRate::updateOrCreate(
                        ['currency' => $baseCurrency, 'date' => $date],
                        ['rate' => $rate]
                    );
                }

            }

            $this->info('Exchange rates fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch exchange rates.');
        }
    }
}
