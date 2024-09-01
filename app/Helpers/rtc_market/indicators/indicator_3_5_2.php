<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_5_2
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
        $query = HouseholdRtcConsumption::query()->where('status', 'approved');

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // NO data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }


        if ($this->organisation_id && $this->target_year_id) {
            $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
            $query = $data;

        } else
            if ($this->organisation_id && $this->target_year_id == null) {
                $data = $query->where('organisation_id', $this->organisation_id);
                $query = $data;

            }


        return $query;
    }

    public function getDisaggregations()
    {
        $total = $this->builder()->sum('rtc_consumption_frequency');
        return [
            'Total' => $total
        ];
    }
}
