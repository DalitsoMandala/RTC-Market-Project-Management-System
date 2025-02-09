<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Models\HouseholdRtcConsumption;
use App\Models\SchoolRtcConsumption;
use Illuminate\Database\Eloquent\Builder;


class indicator_B4
{
    use FilterableQuery;
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

        return $this->applyFilters($query);
    }

    public function builderSchool(): Builder
    {

        $query = SchoolRtcConsumption::query()->where('status', 'approved');

        return $this->applyFilters($query);
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
