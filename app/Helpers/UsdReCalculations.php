<?php


namespace App\Helpers;

use App\Traits\EnsureNumericTrait;
use function Symfony\Component\String\s;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
        $producePrevailingPrice = (float) $producePrevailingPrice == 0 ? 1 : $producePrevailingPrice;

        $seed = (float) $seed;
        $seedPrevailingPrice = (float) $seedPrevailingPrice == 0 ? 1 : $seedPrevailingPrice;

        $cuttings = (float) $cuttings;
        $cuttingsPrevailingPrice = (float) $cuttingsPrevailingPrice == 0 ? 1 : $cuttingsPrevailingPrice;

        return ($produce * $producePrevailingPrice) +
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


    public function checkRowsThatHaveNoUsdValue(Builder $builder, bool $production = true)
    {
        try {
            DB::beginTransaction();

            if($production){
                $builder->get()->each(function ($row) {

                $prod_value_previous_season_total = $this->calculateTotalProduction(
                    produce: $this->ensureNumeric($row->prod_value_previous_season_produce),
                    producePrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price),
                    seed: $this->ensureNumeric($row->prod_value_previous_season_seed),
                    seedPrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price),
                    cuttings: $this->ensureNumeric($row->prod_value_previous_season_cuttings),
                    cuttingsPrevailingPrice: $this->ensureNumeric($row->prod_value_cuttings_prevailing_price)
                );

                $irr_prod_value_previous_season_total = $this->calculateTotalProduction(
                    produce: $this->ensureNumeric($row->irr_prod_value_previous_season_produce),
                    producePrevailingPrice: $this->ensureNumeric($row->irr_prod_value_produce_prevailing_price),
                    seed: $this->ensureNumeric($row->irr_prod_value_previous_season_seed),
                    seedPrevailingPrice: $this->ensureNumeric($row->irr_prod_value_produce_prevailing_price),
                    cuttings: $this->ensureNumeric($row->irr_prod_value_previous_season_cuttings),
                    cuttingsPrevailingPrice: $this->ensureNumeric($row->irr_prod_value_cuttings_prevailing_price)
                );

                $prodDate = $row->prod_value_previous_season_date_of_max_sales;
                $prodUsd = $this->calculateUsdValue($prodDate, $prod_value_previous_season_total);
                $ProdRate = $prodUsd['rate'];
                $ProdValue = $prodUsd['usd_value'];

                $irrProdDate = $row->irr_prod_value_previous_season_date_of_max_sales;
                $irrProdUsd = $this->calculateUsdValue($irrProdDate, $irr_prod_value_previous_season_total);
                $irrProdRate = $irrProdUsd['rate'];
                $irrProdValue = $irrProdUsd['usd_value'];

                $row->update([
                    'prod_value_previous_season_total' => $prod_value_previous_season_total,
                    'prod_value_previous_season_usd_rate' => $ProdRate,
                    'prod_value_previous_season_usd_value' => $ProdValue,
                    'irr_prod_value_previous_season_total' => $irr_prod_value_previous_season_total,
                    'irr_prod_value_previous_season_usd_rate' => $irrProdRate,
                    'irr_prod_value_previous_season_usd_value' => $irrProdValue
                ]);
            });
            } else{
                  $builder->get()->each(function ($row) {

                $prod_value_previous_season_total = $this->calculateTotalProduction(
                    produce: $this->ensureNumeric($row->prod_value_previous_season_produce),
                    producePrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price),
                    seed: $this->ensureNumeric($row->prod_value_previous_season_seed),
                    seedPrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price),
                    cuttings: $this->ensureNumeric($row->prod_value_previous_season_cuttings),
                    cuttingsPrevailingPrice: $this->ensureNumeric($row->prod_value_cuttings_prevailing_price)
                );

                $prodDate = $row->prod_value_previous_season_date_of_max_sales;
                $prodUsd = $this->calculateUsdValue($prodDate, $prod_value_previous_season_total);
                $ProdRate = $prodUsd['rate'];
                $ProdValue = $prodUsd['usd_value'];



                $row->update([
                    'prod_value_previous_season_total' => $prod_value_previous_season_total,
                    'prod_value_previous_season_usd_rate' => $ProdRate,
                    'prod_value_previous_season_usd_value' => $ProdValue,

                ]);
            });
            }



            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
