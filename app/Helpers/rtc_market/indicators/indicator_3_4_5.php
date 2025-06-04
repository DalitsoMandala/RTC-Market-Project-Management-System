<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_4_5
{
    use FilterableQuery;
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;


    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }
    // public function builder(): Builder
    // {

    //     $query = RtcProductionFarmer::query()->where('status', 'approved')
    //         ->where('approach', 'Collective marketing only')
    //         ->where('type', 'Producer organization (PO)');



    //     // Check if both reporting period and financial year are set
    //     if ($this->reporting_period || $this->financial_year) {
    //         // Apply filter for reporting period if it's set
    //         if ($this->reporting_period) {
    //             $query->where('period_month_id', $this->reporting_period);
    //         }

    //         // Apply filter for financial year if it's set
    //         if ($this->financial_year) {
    //             $query->where('financial_year_id', $this->financial_year);
    //         }

    //         // If no data is found, return an empty result
    //         if (!$query->exists()) {
    //             $query->whereIn('id', []); // Empty result filter
    //         }
    //     }

    //     // Filter by organization if set
    //     if ($this->organisation_id) {
    //         $query->where('organisation_id', $this->organisation_id);
    //     }




    //     return $query;
    // }
    // public function getTotals()
    // {

    //     $totalPotato = $this->builder()->where('enterprise', 'Potato')->count();
    //     $totalCassava = $this->builder()->where('enterprise', 'Cassava')->count();
    //     $totalSweetPotato = $this->builder()->where('enterprise', 'Sweet potato')->count();


    //     return [
    //         'Potato' => $totalPotato,
    //         'Cassava' => $totalCassava,
    //         'Sweet potato' => $totalSweetPotato,
    //     ];
    // }
    // public function getMarketSegment()
    // {
    //     return $this->builder()->selectRaw('SUM(IF(market_segment_fresh = 1, 1, 0)) as Fresh, SUM(IF(market_segment_processed = 1, 1, 0)) as Processed')->first()->toArray();
    // }

    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Volume (MT) of RTC products sold through collective marketing efforts by POs')->where('indicator_no', '3.4.5')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');


        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }




        return $this->applyFilters($query, true);
    }

    public function getTotals()
    {

        $builder = $this->builder()->get();

        $indicator = Indicator::where('indicator_name', 'Volume (MT) of RTC products sold through collective marketing efforts by POs')->where('indicator_no', '3.4.5')->first();
        $disaggregations = $indicator->disaggregations;
        $data = collect([]);
        $disaggregations->pluck('name')->map(function ($item) use (&$data) {
            $data->put($item, 0);
        });




        $this->builder()->chunk(1000, function ($models) use (&$data) {
            $models->each(function ($model) use (&$data) {
                // Decode the JSON data from the model
                $json = collect(json_decode($model->data, true));

                // Add the values for each key to the totals
                foreach ($data as $key => $dt) {
                    // Always process non-enterprise keys
                    $isEnterpriseKey = str_contains($key, 'Cassava') ||
                        str_contains($key, 'Potato') ||
                        str_contains($key, 'Sweet potato');

                    // If enterprise is set, only process matching keys or non-enterprise keys
                    if (!$this->enterprise || !$isEnterpriseKey || str_contains($key, $this->enterprise)) {
                        if ($json->has($key)) {
                            $data->put($key, $data->get($key) + $json[$key]);
                        }
                    }
                }
            });
        });

        return $data;
    }
    public function getDisaggregations()
    {
        $total = $this->getTotals()['Cassava'] + $this->getTotals()['Potato'] + $this->getTotals()['Sweet potato'];

        return [
            'Total' => $total,
            'Cassava' => $this->getTotals()['Cassava'],
            'Potato' => $this->getTotals()['Potato'],
            'Sweet potato' => $this->getTotals()['Sweet potato'],
            'Fresh' => $this->getTotals()['Fresh'] ?? 0,
            'Processed' => $this->getTotals()['Processed'] ?? 0,
        ];
    }
}