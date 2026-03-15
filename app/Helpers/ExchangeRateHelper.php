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
    protected $apiKey;
    protected $baseCurrency = 'USD';

    public function __construct()
    {
        $this->apiKey = config('services.currency_beacon.key');
    }

    public function getRate($totalValue = null, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return Cache::remember("exchange_rate_{$date}", 86400, function () use ($date) {

            $rateRecord = ExchangeRate::where('date', $date)
                ->where('currency', $this->baseCurrency)
                ->first();

            if ($rateRecord) {
                return $rateRecord->rate;
            }

            try {
                $url = "https://api.currencybeacon.com/v1/historical?api_key={$this->apiKey}&base={$this->baseCurrency}&date={$date}";
                $response = Http::get($url);

                $data = $response->json();

                if ($response->successful() && isset($data['response']['rates']['MWK'])) {

                    $rate = $data['response']['rates']['MWK'];

                    ExchangeRate::updateOrCreate(
                        [
                            'currency' => $this->baseCurrency,
                            'date' => $date,
                        ],
                        ['rate' => $rate]
                    );

                    return $rate;
                }

                throw new Exception('Exchange rate not found.');
            } catch (Exception $e) {

                Log::error('Exchange rate retrieval error: ' . $e->getMessage(), [
                    'date' => $date,
                    'base_currency' => $this->baseCurrency
                ]);

                return null;
            }
        });
    }
}
