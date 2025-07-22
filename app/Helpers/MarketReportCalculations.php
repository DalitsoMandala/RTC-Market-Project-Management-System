<?php

namespace App\Helpers;

use App\Models\MarketData;
use App\Models\ReportStatus;

use App\Models\MarketDataReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class MarketReportCalculations
{
    //
    protected  $years = [];

    public function __construct()
    {
        //

        $getYears = MarketData::query()->select([
            DB::raw('YEAR(entry_date) as year')
        ])->groupBy('year')->get();

        foreach ($getYears as $year) {
            $this->years[] = $year->year;
        }
    }

    public function run()
    {

        try {
            # code...
            $dataArray = [
                'volumeVsValueByDistrict' => $this->volumeVsValueByDistrict(),
                'demandByVariety' => $this->demandByVariety(),
                'valueByVariety' => $this->valueByVariety(),
                'monthlyVolumeVsValue' => $this->monthlyVolumeVsValue(),
                'monthlyDemandByVariety' => $this->monthlyDemandByVariety(),
                'countryValueShare' => $this->countryValueShare(),
                'demandByCountryAndVariety' => $this->demandByCountryAndVariety(),
                'priceTrendMWK' => $this->priceTrendMWK(),


            ];




            foreach ($dataArray as $dataName => $yearArray) {

                foreach ($yearArray as $year => $data) {

                    MarketDataReport::updateOrCreate([
                        'name' => $dataName,
                        'date' => $year
                    ], [
                        'data' => json_encode($data)

                    ]);
                }
            }
        } catch (\Throwable $e) {
            # code...
            Log::error($e);
        }
    }

    public function builder(): Builder
    {
        return MarketData::query();
    }

    public function volumeVsValueByDistrict()
    {
        return $this->getDataGroupedByYear(function () {
            return $this->builder()
                ->select([
                    'final_market_district',
                    DB::raw('SUM(estimated_demand_kg) AS volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) AS usd_value'),
                ])
                ->groupBy('final_market_district')
                ->orderBy('final_market_district');
        }, $this->years);
    }

    public function demandByVariety()
    {
        return $this->getDataGroupedByYear(function () {
            return $this->builder()
                ->select([
                    'variety_demanded',
                    DB::raw('SUM(estimated_demand_kg) AS volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) AS usd_value'),
                ])
                ->groupBy('variety_demanded')
                ->orderBy('variety_demanded');
        }, $this->years);
    }

    public function valueByVariety()
    {
        return $this->getDataGroupedByYear(function () {
            return $this->builder()
                ->select([
                    'variety_demanded',
                    DB::raw('SUM(estimated_demand_kg) AS volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) AS usd_value'),
                ])->groupBy('variety_demanded')
                ->orderBy('variety_demanded');
        }, $this->years);
    }

    public function monthlyVolumeVsValue()
    {
        return $this->getDataGroupedByYear(function () {
            return $this->builder()
                ->select([
                    'entry_date',
                    DB::raw('SUM(estimated_demand_kg) AS volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) AS usd_value'),
                ])
                ->groupBy('entry_date')
                ->orderBy('entry_date');
        }, $this->years);
    }

    public function monthlyDemandByVariety()
    {

        return $this->getGroupedVarietyByYear(function () {
            return $this->builder()
                ->select([
                    'variety_demanded',
                    'entry_date',
                    DB::raw('SUM(estimated_demand_kg) as volume_kg'),
                ])
                ->groupBy('entry_date', 'variety_demanded')
                ->orderBy('entry_date');
        }, $this->years);
    }

    public function countryValueShare()
    {
        return $this->getGroupedCountryShareGroupedByYear(function () {
            return MarketData::query()
                ->select([
                    'final_market_country',
                    DB::raw('SUM(estimated_demand_kg) as volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) as usd_value'),
                ])
                ->groupBy('final_market_country')
                ->orderBy('final_market_country');
        }, $this->years);
    }

    public function demandByCountryAndVariety()
    {
        return $this->getGroupedVarietyCountryByYear(function () {
            return MarketData::query()
                ->select([
                    'variety_demanded',
                    'final_market_country',
                    DB::raw('SUM(estimated_demand_kg) as volume_kg'),
                ])
                ->groupBy('variety_demanded', 'final_market_country')
                ->orderBy('variety_demanded');
        }, $this->years);
    }
    public function priceTrendMWK()
    {
        return $this->getAveragePricePerKgByYear(function () {
            return MarketData::query()
                ->select([
                    'entry_date',
                    DB::raw('SUM(estimated_demand_kg) as volume_kg'),
                    DB::raw('SUM(agreed_price_per_kg) as total_price'),
                    DB::raw('ROUND(SUM(agreed_price_per_kg) / NULLIF(SUM(estimated_demand_kg), 0), 2) as avg_price_per_kg')
                ])
                ->groupBy('entry_date')
                ->orderBy('entry_date');
        }, $this->years);
    }
    private function getGroupedVarietyByYear(callable $builderCallback, array $years): array
    {
        $result = [];

        foreach ($years as $year) {
            $grouped = [];

            $builderCallback()
                ->whereYear('entry_date', $year)
                ->chunk(1000, function ($rows) use (&$grouped) {
                    foreach ($rows as $row) {
                        $date = $row->entry_date;
                        $variety = $row->variety_demanded;
                        $volume = (float)$row->volume_kg;

                        if (!isset($grouped[$date])) {
                            $grouped[$date] = [];
                        }

                        if (!isset($grouped[$date][$variety])) {
                            $grouped[$date][$variety] = [];
                        }

                        $grouped[$date][$variety] = +$volume;
                    }
                });

            $result[$year] = $grouped;
        }



        // 'All' (no year filter)
        $groupedAll = [];

        $builderCallback()
            ->chunk(1000, function ($rows) use (&$groupedAll) {
                foreach ($rows as $row) {
                    $date = $row->entry_date;
                    $variety = $row->variety_demanded;
                    $volume = (float)$row->volume_kg;

                    if (!isset($groupedAll[$date])) {
                        $groupedAll[$date] = [];
                    }

                    if (!isset($groupedAll[$date][$variety])) {
                        $groupedAll[$date][$variety] = [];
                    }

                    $groupedAll[$date][$variety] = +$volume;
                }
            });

        $result['All'] = $groupedAll;

        return $result;
    }





    private function getAveragePricePerKgByYear(callable $builderCallback, array $years): array
    {
        $results = [];

        foreach ($years as $year) {
            $yearlyData = [];

            $builderCallback()
                ->whereYear('entry_date', $year)
                ->chunk(1000, function ($rows) use (&$yearlyData) {
                    foreach ($rows as $row) {
                        $yearlyData[] = $row->toArray();
                    }
                });

            $results[$year] = $yearlyData;
        }

        // All years combined
        $allData = [];

        $builderCallback()
            ->chunk(1000, function ($rows) use (&$allData) {
                foreach ($rows as $row) {
                    $allData[] = $row->toArray();
                }
            });

        $results['All'] = $allData;

        return $results;
    }

    private function getGroupedVarietyCountryByYear(callable $builderCallback, array $years): array
    {
        $results = [];

        foreach ($years as $year) {
            $grouped = [];

            $builderCallback()
                ->whereYear('entry_date', $year)
                ->chunk(1000, function ($rows) use (&$grouped) {
                    foreach ($rows as $row) {
                        $variety = $row->variety_demanded;
                        $country = $row->final_market_country;
                        $volume = (float) $row->volume_kg;

                        if (!isset($grouped[$variety])) {
                            $grouped[$variety] = [];
                        }

                        if (!isset($grouped[$variety][$country])) {
                            $grouped[$variety][$country] = 0;
                        }

                        $grouped[$variety][$country] += $volume;
                    }
                });

            $results[$year] = $grouped;
        }

        // "All" years (no filter)
        $groupedAll = [];

        $builderCallback()
            ->chunk(1000, function ($rows) use (&$groupedAll) {
                foreach ($rows as $row) {
                    $variety = $row->variety_demanded;
                    $country = $row->final_market_country;
                    $volume = (float) $row->volume_kg;

                    if (!isset($groupedAll[$variety])) {
                        $groupedAll[$variety] = [];
                    }

                    if (!isset($groupedAll[$variety][$country])) {
                        $groupedAll[$variety][$country] = 0;
                    }

                    $groupedAll[$variety][$country] += $volume;
                }
            });

        $results['All'] = $groupedAll;

        return $results;
    }


    private function getDataGroupedByYear(callable $builderCallback, array $years): array
    {
        $results = [];

        foreach ($years as $year) {
            $yearlyData = [];

            $builderCallback()
                ->whereYear('entry_date', $year)
                ->chunk(1000, function ($rows) use (&$yearlyData) {
                    foreach ($rows as $row) {
                        $yearlyData[] = $row->toArray();
                    }
                });

            $results[$year] = $yearlyData;
        }

        // "All" dataset (no year filter)
        $allData = [];

        $builderCallback()
            ->chunk(1000, function ($rows) use (&$allData) {
                foreach ($rows as $row) {
                    $allData[] = $row->toArray();
                }
            });

        $results['All'] = $allData;

        return $results;
    }


    private function getGroupedCountryShareGroupedByYear(callable $builderCallback, array $years): array
    {
        $results = [];

        foreach ($years as $year) {
            $yearlyData = [];
            $totalUSD = 0;

            // Step 1: Get total USD value (from all groups)
            $builderCallback()
                ->whereYear('entry_date', $year)
                ->chunk(1000, function ($rows) use (&$totalUSD) {
                    foreach ($rows as $row) {
                        $totalUSD += (float) $row->usd_value;
                    }
                });

            // Step 2: Collect share per country
            $builderCallback()
                ->whereYear('entry_date', $year)
                ->chunk(1000, function ($rows) use (&$yearlyData, $totalUSD) {
                    foreach ($rows as $row) {
                        $yearlyData[] = [
                            'country' => $row->final_market_country,
                            'share' => $totalUSD > 0 ? round($row->usd_value / $totalUSD * 100, 2) : 0,
                        ];
                    }
                });

            $results[$year] = $yearlyData;
        }

        // Handle "All" years
        $totalUSDAll = 0;
        $allData = [];

        // Step 1: Sum usd_value
        $builderCallback()
            ->chunk(1000, function ($rows) use (&$totalUSDAll) {
                foreach ($rows as $row) {
                    $totalUSDAll += (float) $row->usd_value;
                }
            });

        // Step 2: Calculate share
        $builderCallback()
            ->chunk(1000, function ($rows) use (&$allData, $totalUSDAll) {
                foreach ($rows as $row) {
                    $allData[] = [
                        'country' => $row->final_market_country,
                        'share' => $totalUSDAll > 0 ? round($row->usd_value / $totalUSDAll * 100, 2) : 0,
                    ];
                }
            });

        $results['All'] = $allData;

        return $results;
    }
}
