<?php


namespace App\Helpers;

use App\Traits\EnsureNumericTrait;
use function Symfony\Component\String\s;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class UsdReCalculations
{
    use EnsureNumericTrait;
    public function __construct() {}

 public function calculateTotalProduction(
    $produce,
    $producePrevailingPrice,
    $seed,
    $seedPrevailingPrice,
    $cuttings,
    $cuttingsPrevailingPrice
) {

    $produce = (float) $produce;
    $producePrevailingPrice = (float) $producePrevailingPrice;

    $seed = (float) $seed;
    $seedPrevailingPrice = (float) $seedPrevailingPrice;

    $cuttings = (float) $cuttings;
    $cuttingsPrevailingPrice = (float) $cuttingsPrevailingPrice;

    return
        ($produce * $producePrevailingPrice) +
        ($seed * $seedPrevailingPrice) +
        ($cuttings * $cuttingsPrevailingPrice);
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
        $builder->get()->each(function ($row) {

            $prod_value_previous_season_total = $this->calculateTotalProduction(
                produce: $this->ensureNumeric($row->prod_value_previous_season_produce),
                producePrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price == 0 ? 1 : $row->prod_value_produce_prevailing_price),
                seed: $this->ensureNumeric($row->prod_value_previous_season_seed),
                seedPrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price == 0 ? 1 : $row->prod_value_seed_prevailing_price),
                cuttings: $this->ensureNumeric($row->prod_value_previous_season_cuttings),
                cuttingsPrevailingPrice: $this->ensureNumeric($row->prod_value_cuttings_prevailing_price == 0 ? 1 : $row->prod_value_cuttings_prevailing_price)
            );


            dd($prod_value_previous_season_total);
        });
    }
}
