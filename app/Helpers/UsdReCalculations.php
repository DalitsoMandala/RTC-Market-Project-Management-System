<?php


namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Traits\EnsureNumericTrait;

class UsdReCalculations
{

public function __construct(){

}

    public function calculateTotalProduction($produce, $producePrevailingPrice, $seed, $seedPrevailingPrice, $cuttings, $cuttingsPrevailingPrice)
    {
        $produce = (float) ($produce ?? 0);
        $producePrevailingPrice = (float) ($producePrevailingPrice ?? 0);
        $seed = (float) ($seed ?? 0);
        $seedPrevailingPrice = (float) ($seedPrevailingPrice ?? 0);
        $cuttings = (float) ($cuttings ?? 0);
        $cuttingsPrevailingPrice = (float) ($cuttingsPrevailingPrice ?? 0);

        $totalProduction = ($produce * $producePrevailingPrice)
            + ($seed * $seedPrevailingPrice)
            + ($cuttings * $cuttingsPrevailingPrice);

        return $totalProduction;
    }

    private function calculateUsdValue(?string $date, ?float $mwkValue): array
    {
        if (!$date || !$mwkValue) {
            return ['rate' => 0, 'usd_value' => 0];
        }

        try {
            $helper = new \App\Helpers\ExchangeRateHelper();
            $rate = $helper->getRate($mwkValue, $date);
            $usdValue = $rate ? round($mwkValue / $rate, 2) : 0;
            return ['rate' => $rate, 'usd_value' => $usdValue];
        } catch (\Exception $e) {
            Log::error("Exchange rate calc error: " . $e->getMessage());
            return ['rate' => 0, 'usd_value' => 0];
        }
    }

    public function checkRowsThatHaveNoUsdValue(Builder $builder)
    {
       $builder->get()->map(function ($row) {
          dd($row);
       });

    }

}
