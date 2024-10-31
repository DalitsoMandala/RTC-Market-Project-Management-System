<?php

namespace App\Helpers;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class ExchangeRateHelper
{

    protected $apiKey = null; // Store your API key in config/services.php
    protected $baseCurrency = 'USD'; // or any other base currency

    /**
     * Get the exchange rate for a given date, with caching and error handling.
     *
     * @param float $totalValue
     * @param string $date
     * @return float|null
     */

    public function __construct()
    {
        $this->apiKey = env('CURRENCY_BEACON_API');
    }
    public function getRate($totalValue, $date)
    {
        // Format the date
        $date = Carbon::parse($date)->format('Y-m-d');


        // Check if the rate exists in the database
        $rateRecord = ExchangeRate::where('date', $date)->where('currency', $this->baseCurrency)->first();

        if ($rateRecord) {


            return $rateRecord->rate;
        }



        // If not found, request from the API
        try {

            $url = "https://api.currencybeacon.com/v1/historical?api_key={$this->apiKey}&base={$this->baseCurrency}&date={$date}";
            $response = Http::get($url);

            // Validate the response
            if ($response->successful() && isset($response['response']['rates']['MWK'])) {
                $rate = $response['response']['rates']['MWK'];

                // Save the rate in the database for future requests
                ExchangeRate::updateOrCreate(
                    [
                        'currency' => $this->baseCurrency,
                        'date' => $date
                    ],
                    ['rate' => $rate]
                );

                return $rate;
            } else {
                // Log the error or handle it accordingly if the response is unsuccessful
                throw new Exception('Exchange rate not found in API response.');
            }
        } catch (Exception $e) {
            // Log the exception for debugging
            \Log::error('Exchange rate retrieval error: ' . $e->getMessage());
            return null; // Return null if an error occurs
        }

    }
}
