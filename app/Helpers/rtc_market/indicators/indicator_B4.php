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


    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }
    public function findTotalMembersNutrition()
    {
        $totalFemale = 0;
        $totalMale = 0;
        $totalYouth = 0;
        $totalAdult = 0;
        $this->builder()->chunk(1000, function ($data) use (&$totalFemale, &$totalMale, &$totalYouth, &$totalAdult) {
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
        $this->builder()->chunk(1000, function ($data) use (&$totalFemale, &$totalMale, &$totalYouth, &$totalAdult) {
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

        $this->builder()->chunk(1000, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
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

        $this->builder()->chunk(1000, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
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

        return $this->applyHouseHoldFilters($query);
    }

    public function builderHousehold(): Builder
    {
        $query = RtcConsumption::query()->where('status', 'approved')->where('entity_type', 'Nutrition intervention group');

        return $this->applyHouseHoldFilters($query);
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


    public function getMainGroup($type = null, $enterprise = null, $estType = null): Builder
    {


        $builder =   $this->builder()->select([
            'enterprise',
            'type',
            'mem_female_18_35',
            'mem_female_35_plus',
            'mem_male_18_35',
            'mem_male_35_plus',
            'emp_formal_female_18_35',
            'emp_formal_female_35_plus',
            'emp_formal_male_18_35',
            'emp_formal_male_35_plus',
            'emp_informal_female_18_35',
            'emp_informal_female_35_plus',
            'emp_informal_male_18_35',
            'emp_informal_male_35_plus',
        ]);


        if ($type) {
            $builder->where('type', $type);
        }

        if ($enterprise) {
            $builder->where('enterprise', $enterprise);
        }

        if ($estType) {
            $builder->where('establishment_status', $estType);
        }

        return $builder;
    }

    public function getTotalSum($type = null, $enterprise = null, $estType = null)
    {
        $builder = $this->getMainGroup();


        // Initialize totals
        $totals = [
            'members' => 0,
            'employees' => 0,
            'male' => 0,
            'female' => 0,
            'youth' => 0,
            'not_youth' => 0,
        ];

        if ($type) {
            $builder =  $this->getMainGroup(type: $type);
        }

        if ($enterprise) {
            $builder =  $this->getMainGroup(enterprise: $enterprise);
        }

        if ($estType) {
            $builder =  $this->getMainGroup(estType: $estType);
        }


        $data = $builder->get();


        // Loop through each record and sum
        foreach ($data as $row) {
            $female_members = $row->mem_female_18_35 + $row->mem_female_35_plus;
            $male_members = $row->mem_male_18_35 + $row->mem_male_35_plus;

            $female_employees = $row->emp_formal_female_18_35 + $row->emp_formal_female_35_plus +
                $row->emp_informal_female_18_35 + $row->emp_informal_female_35_plus;

            $male_employees = $row->emp_formal_male_18_35 + $row->emp_formal_male_35_plus +
                $row->emp_informal_male_18_35 + $row->emp_informal_male_35_plus;

            $totals['members'] += $female_members + $male_members;
            $totals['employees'] += $female_employees + $male_employees;

            $totals['female'] += $female_members + $female_employees;
            $totals['male'] += $male_members + $male_employees;

            $totals['youth'] += $row->mem_female_18_35 + $row->mem_male_18_35 + $row->emp_formal_female_18_35 + $row->emp_formal_male_18_35 + $row->emp_informal_female_18_35 + $row->emp_informal_male_18_35;
            $totals['not_youth'] += $row->mem_female_35_plus + $row->mem_male_35_plus + $row->emp_formal_female_35_plus + $row->emp_formal_male_35_plus + $row->emp_informal_female_35_plus + $row->emp_informal_male_35_plus;
        }


        return $totals;
    }
    public function getDisaggregations()
    {


        $household = ($this->getTotalSum()['employees'] + $this->getTotalSum()['members']) * 5;
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
