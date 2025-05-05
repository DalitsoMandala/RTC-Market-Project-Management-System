<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Models\HouseholdRtcConsumption;
use App\Models\Recruitment;
use App\Models\RtcConsumption;
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
    public function findTotalMembersNutrition()
    {
        $totalFemale = 0;
        $totalMale = 0;
        $totalYouth = 0;
        $totalAdult = 0;
        $this->builder()->where('group', 'Other')->chunk(100, function ($data) use (&$totalFemale, &$totalMale, &$totalYouth, &$totalAdult) {
            foreach ($data as $model) {

                $totalFemale += $model->mem_female_18_35 + $model->mem_female_35_plus;
                $totalMale += $model->mem_male_18_35 + $model->mem_male_35_plus;
                $totalYouth += $model->mem_female_18_35 + $model->mem_male_18_35;
                $totalAdult += $model->mem_female_35_plus + $model->mem_male_35_plus;
            }
        });

        return [
            'totalFemale' => $totalFemale,
            'totalMale' => $totalMale,
            'totalYouth' => $totalYouth,
            'totalAdult' => $totalAdult,
            'TotalMembers' => $totalFemale + $totalMale
        ];
    }

    public function findTotalMembersHousehold()
    {
        $totalFemale = 0;
        $totalMale = 0;
        $totalYouth = 0;
        $totalAdult = 0;
        $this->builder()->where('group', '!=', 'Other')->chunk(100, function ($data) use (&$totalFemale, &$totalMale, &$totalYouth, &$totalAdult) {
            foreach ($data as $model) {

                $totalFemale += $model->mem_female_18_35 + $model->mem_female_35_plus;
                $totalMale += $model->mem_male_18_35 + $model->mem_male_35_plus;
                $totalYouth += $model->mem_female_18_35 + $model->mem_male_18_35;
                $totalAdult += $model->mem_female_35_plus + $model->mem_male_35_plus;
            }
        });

        return [
            'totalFemale' => $totalFemale,
            'totalMale' => $totalMale,
            'totalYouth' => $totalYouth,
            'totalAdult' => $totalAdult,
            'TotalMembers' => $totalFemale + $totalMale
        ];
    }
    public function findTotalEmployeesHousehold()
    {
        $totalEmpFormal = 0;
        $totalEmpInFormal = 0;

        $this->builder()->where('group', '!=', 'Other')->chunk(100, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
            foreach ($data as $model) {
                $model->empFormalTotal = $model->emp_formal_female_18_35
                    + $model->emp_formal_male_18_35
                    + $model->emp_formal_male_35_plus
                    + $model->emp_formal_female_35_plus;

                $model->empInFormalTotal = $model->emp_informal_female_18_35
                    + $model->emp_informal_male_18_35
                    + $model->emp_informal_male_35_plus
                    + $model->emp_informal_female_35_plus;

                $totalEmpFormal += $model->empFormalTotal;
                $totalEmpInFormal += $model->empInFormalTotal;
            }
        });

        return [
            'totalEmpFormal' => $totalEmpFormal,
            'totalEmpInFormal' => $totalEmpInFormal,
            'Total' => ($totalEmpFormal + $totalEmpInFormal)
        ];
    }

    public function findTotalEmployeesNutrition()
    {
        $totalEmpFormal = 0;
        $totalEmpInFormal = 0;

        $this->builder()->where('group', 'Other')->chunk(100, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
            foreach ($data as $model) {
                $model->empFormalTotal = $model->emp_formal_female_18_35
                    + $model->emp_formal_male_18_35
                    + $model->emp_formal_male_35_plus
                    + $model->emp_formal_female_35_plus;

                $model->empInFormalTotal = $model->emp_informal_female_18_35
                    + $model->emp_informal_male_18_35
                    + $model->emp_informal_male_35_plus
                    + $model->emp_informal_female_35_plus;

                $totalEmpFormal += $model->empFormalTotal;
                $totalEmpInFormal += $model->empInFormalTotal;
            }
        });

        return [
            'totalEmpFormal' => $totalEmpFormal,
            'totalEmpInFormal' => $totalEmpInFormal,
            'Total' => ($totalEmpFormal + $totalEmpInFormal)
        ];
    }
    public function builder(): Builder
    {

        $query = Recruitment::query()->where('status', 'approved');

        return $this->applyFilters($query);
    }

    public function builderSchool(): Builder
    {

        $query = RtcConsumption::query()->where('status', 'approved')->where('entity_type', 'School');

        return $this->applyFilters($query);
    }

    public function getDisaggregations()
    {



        $household = ($this->findTotalMembersHousehold()['TotalMembers']  + $this->findTotalEmployeesHousehold()['Total']) * 5;
        $interventions = ($this->findTotalMembersNutrition()['TotalMembers']  + $this->findTotalEmployeesNutrition()['Total']) * 5;
        $school = $this->builderSchool()->count();
        $total = $household + $interventions + $school;
        return [
            "Total" => $total,
            "RTC actors and households" => $household,
            "School feeding beneficiaries" => $school,
            "Individuals from households reached with nutrition interventions" => $interventions,
        ];
    }
}
