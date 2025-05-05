<?php



namespace App\Helpers;

use Exception;
use Carbon\Carbon;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateHelper
{
    protected $apiKey = null;
    protected $baseCurrency = 'USD';

    public function __construct()
    {
        $this->apiKey = env('CURRENCY_BEACON_API');
    }

    public function getRate($totalValue, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        // Cache key unique to date and currency
        $cacheKey = "exchange_rate_{$this->baseCurrency}_{$date}";

        // First, check if it's already in cache (super fast)
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($date) {

            // Check if the rate exists in the database
            $rateRecord = ExchangeRate::where('date', $date)
                ->where('currency', $this->baseCurrency)
                ->first();

            if ($rateRecord) {
                return $rateRecord->rate;
            }

            // If not found in DB, request from the API
            try {
                $url = "https://api.currencybeacon.com/v1/historical?api_key={$this->apiKey}&base={$this->baseCurrency}&date={$date}";
                $response = Http::withoutVerifying()->get($url);

                if ($response->successful() && isset($response['response']['rates']['MWK'])) {
                    $rate = $response['response']['rates']['MWK'];

                    // Save the rate in the database
                    ExchangeRate::updateOrCreate(
                        [
                            'currency' => $this->baseCurrency,
                            'date' => $date,
                        ],
                        ['rate' => $rate]
                    );

                    return $rate;
                } else {
                    throw new Exception('Exchange rate not found in API response.');
                }
            } catch (Exception $e) {
                Log::error('Exchange rate retrieval error: ' . $e->getMessage());
                return null;
            }
        });
    }
}