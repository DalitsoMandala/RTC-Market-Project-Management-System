<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_5_4
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;



    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $target_year_id;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;

    }
    public function builder(): Builder
    {
        $query = HouseholdRtcConsumption::query()->with('mainFoods')->where('status', 'approved');

        // Check if both reporting period and financial year are set
        if ($this->reporting_period || $this->financial_year) {
            // Apply filter for reporting period if it's set
            if ($this->reporting_period) {
                $query->where('period_month_id', $this->reporting_period);
            }

            // Apply filter for financial year if it's set
            if ($this->financial_year) {
                $query->where('financial_year_id', $this->financial_year);
            }

            // If no data is found, return an empty result
            if (!$query->exists()) {
                $query->whereIn('id', []); // Empty result filter
            }
        }

        // Filter by organization if set
        if ($this->organisation_id) {
            $query->where('organisation_id', $this->organisation_id);
        }


        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }


        return $query;
    }
    public function getDisaggregations()
    {
        $Dishescount = $query = $this->builder()
            ->whereHas('mainFoods', function ($q) {
                $q->whereIn('name', ['Cassava', 'Potato', 'Sweet potato']);
            }, '=', 3) // Ensures all three crops are present
            ->count();

        return [
            'Total' => $Dishescount
        ];
    }
}
