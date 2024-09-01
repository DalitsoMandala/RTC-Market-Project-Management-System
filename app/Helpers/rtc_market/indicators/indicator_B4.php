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


        $total = $this->builder()->count() + $this->builderSchool()->sum('Total');
        $rtcActors = $this->builder()->count();
        $school = $this->builderSchool()->sum('Total');
        $interventions = $this->builder()->where('actor_type', 'INDIVIDUALS FROM NUTRITION INTERVENTION')->count();
        return [
            "Total" => $total,
            "RTC actors and households" => $rtcActors,
            "School feeding beneficiaries" => $school,
            "Individuals from households reached with nutrition interventions" => $interventions,
        ];
    }



}
