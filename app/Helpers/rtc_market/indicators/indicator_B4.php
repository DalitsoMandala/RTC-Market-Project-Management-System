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
        $this->builder()->chunk(100, function ($data) use (&$totalFemale, &$totalMale, &$totalYouth, &$totalAdult) {
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
        $this->builder()->chunk(100, function ($data) use (&$totalFemale, &$totalMale, &$totalYouth, &$totalAdult) {
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

        $this->builder()->chunk(100, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
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

        $this->builder()->chunk(100, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
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

    public function builderHousehold(): Builder
    {
        $query = RtcConsumption::query()->where('status', 'approved')->where('entity_type', 'Nutrition intervention group');

        return $this->applyFilters($query);
    }


    public function findCropBreakdown()
    {
        $results = [];

        $this->builder()
            ->whereIn('type', ['Farmers', 'Processors', 'Traders'])
            ->selectRaw('
            enterprise,
            SUM(emp_formal_female_18_35 + emp_formal_male_18_35 + emp_formal_male_35_plus + emp_formal_female_35_plus) as totalEmployeeFormal,
            SUM(emp_informal_female_18_35 + emp_informal_male_18_35 + emp_informal_male_35_plus + emp_informal_female_35_plus) as totalEmployeeInFormal,
            SUM(mem_female_18_35 + mem_female_35_plus) as totalFemale,
            SUM(mem_male_18_35 + mem_male_35_plus) as totalMale,
            SUM(mem_female_18_35 + mem_male_18_35) as totalYouth,
            SUM(mem_female_35_plus + mem_male_35_plus) as totalAdult,
            SUM(CASE WHEN establishment_status = \'New\' THEN 1 ELSE 0 END) AS new_establishments,
            SUM(CASE WHEN establishment_status = \'Old\' THEN 1 ELSE 0 END) AS old_establishments
        ')
            ->groupBy('enterprise')
            ->orderBy('enterprise') // Helps with consistent chunking
            ->chunk(1000, function ($chunk) use (&$results) {
                foreach ($chunk as $item) {
                    $results[$item->enterprise] = [
                        'totalEmployeeFormal' => (int) $item->totalEmployeeFormal,
                        'totalEmployeeInFormal' => (int) $item->totalEmployeeInFormal,
                        'totalFemale' => (int) $item->totalFemale,
                        'totalMale' => (int) $item->totalMale,
                        'totalYouth' => (int) $item->totalYouth,
                        'totalAdult' => (int) $item->totalAdult,
                        'new_establishments' => (int) $item->new_establishments,
                        'old_establishments' => (int) $item->old_establishments,
                    ];
                }
            });



        return collect($results);
    }

    public function findActorTypeBreakdown()
    {
        $results = [];

        $this->builder()
            ->whereIn('type', ['Farmers', 'Processors', 'Traders'])
            ->selectRaw('
            type,
            SUM(emp_formal_female_18_35 + emp_formal_male_18_35 + emp_formal_male_35_plus + emp_formal_female_35_plus) as totalEmployeeFormal,
            SUM(emp_informal_female_18_35 + emp_informal_male_18_35 + emp_informal_male_35_plus + emp_informal_female_35_plus) as totalEmployeeInFormal,
            SUM(mem_female_18_35 + mem_female_35_plus) as totalFemale,
            SUM(mem_male_18_35 + mem_male_35_plus) as totalMale,
            SUM(mem_female_18_35 + mem_male_18_35) as totalYouth,
            SUM(mem_female_35_plus + mem_male_35_plus) as totalAdult,
            SUM(CASE WHEN establishment_status = \'New\' THEN 1 ELSE 0 END) AS new_establishments,
            SUM(CASE WHEN establishment_status = \'Old\' THEN 1 ELSE 0 END) AS old_establishments
        ')

            ->groupBy('type')
            ->orderBy('type')
            ->chunk(1000, function ($chunk) use (&$results) {
                foreach ($chunk as $item) {
                    $results[$item->type] = [
                        'totalEmployeeFormal' => (int) $item->totalEmployeeFormal,
                        'totalEmployeeInFormal' => (int) $item->totalEmployeeInFormal,
                        'totalFemale' => (int) $item->totalFemale,
                        'totalMale' => (int) $item->totalMale,
                        'totalYouth' => (int) $item->totalYouth,
                        'totalAdult' => (int) $item->totalAdult,
                        'new_establishments' => (int) $item->new_establishments,
                        'old_establishments' => (int) $item->old_establishments,
                    ];
                }
            });



        return collect($results);
    }

    public function getDisaggregations()
    {

        $actorsData = $this->findActorTypeBreakdown();


        $actorTotals = [
            'totalEmployeeFormal' => 0,
            'totalEmployeeInFormal' => 0,
            'totalFemale' => 0,
            'totalMale' => 0,
            'totalYouth' => 0,
            'totalAdult' => 0,
            'new_establishments' => 0,
            'old_establishments' => 0,
        ];

        // Sum all values from actorsData
        foreach ($actorsData as $actor) {
            foreach ($actorTotals as $key => $value) {
                $actorTotals[$key] += isset($actor[$key]) ? $actor[$key] : 0;
            }
        }

        $household = ($actorTotals['totalEmployeeFormal'] + $actorTotals['totalEmployeeInFormal'] + $actorTotals['totalFemale'] + $actorTotals['totalMale']) * 5;
        $interventions = ($this->builderHousehold()->sum('number_of_households')) * 5;
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