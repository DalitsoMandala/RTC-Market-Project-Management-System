<?php

namespace App\Helpers;

use App\Traits\EnsureNumericTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsdReCalculations
{
    use EnsureNumericTrait;

    protected ExchangeRateHelper $exchangeHelper;

    public function __construct()
    {
        $this->exchangeHelper = new ExchangeRateHelper();
    }

    public function calculateTotalProduction(
        $produce,
        $producePrevailingPrice,
        $seed,
        $seedPrevailingPrice,
        $cuttings,
        $cuttingsPrevailingPrice
    ) {

        $produce = (float) $produce;
        $producePrevailingPrice = (float) $producePrevailingPrice ?: 1;

        $seed = (float) $seed;
        $seedPrevailingPrice = (float) $seedPrevailingPrice ?: 1;

        $cuttings = (float) $cuttings;
        $cuttingsPrevailingPrice = (float) $cuttingsPrevailingPrice ?: 1;

        return ($produce * $producePrevailingPrice)
            + ($seed * $seedPrevailingPrice)
            + ($cuttings * $cuttingsPrevailingPrice);
    }

    private function calculateUsdValue(?string $date, ?float $mwkValue): array
    {
        if (!$date || !$mwkValue) {
            return ['rate' => 0, 'usd_value' => 0];
        }

        try {

            $rate = $this->exchangeHelper->getRate(null,$date);

            $usdValue = $rate ? round($mwkValue / $rate, 2) : 0;

            return [
                'rate' => $rate,
                'usd_value' => $usdValue
            ];

        } catch (\Exception $e) {

            Log::error("Exchange rate calc error: " . $e->getMessage());

            return ['rate' => 0, 'usd_value' => 0];
        }
    }

    public function checkRowsThatHaveNoUsdValue(Builder $builder, bool $production = true)
    {

        try {

            DB::beginTransaction();

            $builder->chunkById(200, function ($rows) use ($production) {

                foreach ($rows as $row) {

                    $prodTotal = $this->calculateTotalProduction(
                        produce: $this->ensureNumeric($row->prod_value_previous_season_produce),
                        producePrevailingPrice: $this->ensureNumeric($row->prod_value_produce_prevailing_price),
                        seed: $this->ensureNumeric($row->prod_value_previous_season_seed),
                        seedPrevailingPrice: $this->ensureNumeric($row->prod_value_seed_prevailing_price),
                        cuttings: $this->ensureNumeric($row->prod_value_previous_season_cuttings),
                        cuttingsPrevailingPrice: $this->ensureNumeric($row->prod_value_cuttings_prevailing_price)
                    );

                    $prodUsd = $this->calculateUsdValue(
                        $row->prod_value_previous_season_date_of_max_sales,
                        $prodTotal
                    );



                    $update = [
                        'prod_value_previous_season_total' => $prodTotal,
                        'prod_value_previous_season_usd_rate' => $prodUsd['rate'],
                        'prod_value_previous_season_usd_value' => $prodUsd['usd_value'],
                    ];

                    if ($production) {

                        $irrTotal = $this->calculateTotalProduction(
                            produce: $this->ensureNumeric($row->irr_prod_value_previous_season_produce),
                            producePrevailingPrice: $this->ensureNumeric($row->irr_prod_value_produce_prevailing_price),
                            seed: $this->ensureNumeric($row->irr_prod_value_previous_season_seed),
                            seedPrevailingPrice: $this->ensureNumeric($row->irr_prod_value_seed_prevailing_price),
                            cuttings: $this->ensureNumeric($row->irr_prod_value_previous_season_cuttings),
                            cuttingsPrevailingPrice: $this->ensureNumeric($row->irr_prod_value_cuttings_prevailing_price)
                        );

                        $irrUsd = $this->calculateUsdValue(
                            $row->irr_prod_value_previous_season_date_of_max_sales,
                            $irrTotal
                        );

                        $update = array_merge($update, [
                            'irr_prod_value_previous_season_total' => $irrTotal,
                            'irr_prod_value_previous_season_usd_rate' => $irrUsd['rate'],
                            'irr_prod_value_previous_season_usd_value' => $irrUsd['usd_value'],
                        ]);
                    }


                    $row->update($update);

                    Log::info("Updated USD values for row: {$row->id}");
                }

            });

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('USD recalculation failed: ' . $e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'USD recalculation failed: ' . $e->getMessage()], 500);
        }
    }
}
