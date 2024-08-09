<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Models\HouseholdRtcConsumption;
use App\Models\SchoolRtcConsumption;
use Illuminate\Database\Eloquent\Builder;


class indicator_B4
{
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
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }





        return $query;

    }

    public function builderSchool(): Builder
    {

        $query = SchoolRtcConsumption::query()->where('status', 'approved');

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }





        return $query;

    }

    public function getDisaggregations()
    {


        $total = $this->builder()->count() + $this->builderSchool()->count();
        return [
            "Total" => $total,
            "Volume(Metric Tonnes)" => 0,
            "Financial value ($)" => 0,
            "Cassava" => 0,
            "Potato" => 0,
            "Sweet potato" => 0,
            "Formal" => 0,
            "RTC actors and households" => 0,
            "School feeding beneficiaries" => 0,
            "Individuals from households reached with nutrition interventions" => 0,
        ];
    }



}
