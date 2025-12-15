<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Project;

use App\Models\MarketData;
use App\Models\ReportStatus;
use App\Models\FinancialYear;
use App\Models\MarketDataReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class MarketReportCalculations
{
    //
    protected  $years = [];
    protected  $financialYears = [];
    public function __construct()
    {
        //

        $project = Project::where('name', 'RTC MARKET')->first();
        if (!$project) {
            return;
        }

        $financialYears = FinancialYear::where('project_id', $project->id);

        foreach ($financialYears->get() as $financialYear) {
            $this->financialYears[] = Carbon::parse($financialYear->start_date)->year . '/' . Carbon::parse($financialYear->end_date)->year;
        }


        $this->years = $this->financialYears;
    }

    public function run()
    {

        $market = MarketData::get();
        $market->each(function ($m) {
            if ($m->final_market_district == 'Mzuzu' ) {
                $m->update([
                    'final_market_district' => 'Mzimba'
                ]);
            }

        });

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
                    DB::raw('DATE_FORMAT(entry_date, "%b-%y") AS formatted_date'),
                    DB::raw('SUM(estimated_demand_kg) AS volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) AS usd_value'),
                ])
                ->groupBy('entry_date', 'formatted_date')
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
                    DB::raw('DATE_FORMAT(entry_date, "%b-%y") AS formatted_date'),
                    DB::raw('SUM(estimated_demand_kg) as volume_kg'),
                ])
                ->groupBy('entry_date', 'variety_demanded', 'formatted_date')
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
                    DB::raw('DATE_FORMAT(entry_date, "%b-%y") AS formatted_date'),
                    DB::raw('SUM(estimated_demand_kg) as volume_kg'),
                    DB::raw('SUM(estimated_total_value_usd) as total_price'),
                    DB::raw('ROUND(SUM(estimated_total_value_usd) / NULLIF(SUM(estimated_demand_kg), 0), 2) as avg_price_per_kg')
                ])
                ->groupBy('entry_date', 'formatted_date')
                ->groupBy('entry_date', 'formatted_date')
                ->orderBy('entry_date');
        }, $this->years);
    }
    private function getGroupedVarietyByYear(callable $builderCallback, array $years): array
    {
        $result = [];

        foreach ($years as $year) {
            $yearlyData = [];

            [$year1, $year2] = explode('/', $year);
            $grouped = [];
            $builder = $builderCallback();
            $builder
                ->where(function ($q) use ($year1, $year2) {
                    $q->whereYear('entry_date', $year1)
                        ->orWhereYear('entry_date', $year2);
                })
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

            $yearlyData = [];

            [$year1, $year2] = explode('/', $year);

            $builderCallback()

                ->where(function ($q) use ($year1, $year2) {
                    $q->whereYear('entry_date', $year1)
                        ->orWhereYear('entry_date', $year2);
                })
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

            $yearlyData = [];

            [$year1, $year2] = explode('/', $year);

            // Always get a fresh builder per loop
            $builder = $builderCallback();

            // Group conditions properly
            $builder->where(function ($q) use ($year1, $year2) {
                $q->whereYear('entry_date', $year1)
                    ->orWhereYear('entry_date', $year2);
            })
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

            [$year1, $year2] = explode('/', $year);
            $builder = $builderCallback();

            $builder->whereYear('entry_date', $year)
                ->orWhereYear('entry_date', $year2)
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


            [$year1, $year2] = explode('/', $year);

            // Always get a fresh builder per loop
            $builder = $builderCallback();

            // Group conditions properly
            $builder->where(function ($q) use ($year1, $year2) {
                $q->whereYear('entry_date', $year1)
                    ->orWhereYear('entry_date', $year2);
            })
                ->chunk(1000, function ($rows) use (&$totalUSD) {
                    foreach ($rows as $row) {
                        $totalUSD += (float) $row->usd_value;
                    }
                });

            // Step 2: Collect share per country

            $builder = $builderCallback();

            // Group conditions properly
            $builder->where(function ($q) use ($year1, $year2) {
                $q->whereYear('entry_date', $year1)
                    ->orWhereYear('entry_date', $year2);
            })
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
