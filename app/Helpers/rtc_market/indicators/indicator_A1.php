<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Recruitment;

class indicator_A1
{
    use FilterableQuery;

    protected $financial_year;
    protected $reporting_period;
    protected $organisation_id;
    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }

    public function builder(): Builder
    {
        return $this->applyFilters(
            Recruitment::query()->where('status', 'approved')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PEOPLE TOTALS (EXCLUDING TRADERS)
    |--------------------------------------------------------------------------
    */

    protected function peopleTotals($type = null, $enterprise = null, $estType = null)
    {
        $builder = $this->builder()->where('type', '!=', 'Traders');

        if ($type) {
            $builder->where('type', $type);
        }

        if ($enterprise) {
            $builder->where('enterprise', $enterprise);
        }

        if ($estType) {
            $builder->where('establishment_status', $estType);
        }

        $totals = [
            'members' => 0,
            'employees' => 0,
        ];

        foreach ($builder->get() as $row) {

            $members =
                $row->mem_female_18_35 +
                $row->mem_female_35_plus +
                $row->mem_male_18_35 +
                $row->mem_male_35_plus;

            $employees =
                $row->emp_formal_female_18_35 +
                $row->emp_formal_female_35_plus +
                $row->emp_formal_male_18_35 +
                $row->emp_formal_male_35_plus +
                $row->emp_informal_female_18_35 +
                $row->emp_informal_female_35_plus +
                $row->emp_informal_male_18_35 +
                $row->emp_informal_male_35_plus;

            $totals['members'] += $members;
            $totals['employees'] += $employees;
        }

        return $totals;
    }

    /*
    |--------------------------------------------------------------------------
    | TRADER TOTALS (COUNT ESTABLISHMENTS ONLY)
    |--------------------------------------------------------------------------
    */

    protected function traderTotals()
    {
        $builder = $this->builder()->where('type', 'Traders');

        return [
            'total' => $builder->count(),
            'Cassava' => (clone $builder)->where('enterprise', 'Cassava')->count(),
            'Potato' => (clone $builder)->where('enterprise', 'Potato')->count(),
            'Sweet potato' => (clone $builder)->where('enterprise', 'Sweet potato')->count(),
            'New establishment' => (clone $builder)->where('establishment_status', 'New')->count(),
            'Old establishment' => (clone $builder)->where('establishment_status', 'Old')->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN INDICATOR DISAGGREGATIONS
    |--------------------------------------------------------------------------
    */

    public function getDisaggregations()
    {
        $traders = $this->traderTotals();

        $actors = ['Farmers', 'Processors', 'Aggregators', 'Transporters'];
        $crops = ['Cassava', 'Potato', 'Sweet potato'];

        $actorTotals = [];
        $cropTotals = [];

        $totalMembers = 0;
        $totalEmployees = 0;

        foreach ($actors as $actor) {
            $totals = $this->peopleTotals(type: $actor);

            // Actors = MEMBERS ONLY
            $actorTotals[$actor] = $totals['members'];

            $totalMembers += $totals['members'];
            $totalEmployees += $totals['employees'];
        }

        foreach ($crops as $crop) {
            $totals = $this->peopleTotals(enterprise: $crop);

            $cropTotals[$crop] =
                $totals['members'] +
                $totals['employees'] +
                $traders[$crop];
        }

        $new =
            $this->peopleTotals(estType: 'New')['members'] +
            $this->peopleTotals(estType: 'New')['employees'] +
            $traders['New establishment'];

        $old =
            $this->peopleTotals(estType: 'Old')['members'] +
            $this->peopleTotals(estType: 'Old')['employees'] +
            $traders['Old establishment'];

        /*
        |--------------------------------------------------------------------------
        | FINAL TOTAL (NO DOUBLE COUNTING)
        |--------------------------------------------------------------------------
        | Total = Members + Employees + Traders
        */
        $totalPeople =
            $totalMembers +
            $totalEmployees +
            $traders['total'];

        return [

            'Total' => $totalPeople,

            'Employees on RTC establishment' => $totalEmployees,

            'Cassava' => $cropTotals['Cassava'],
            'Potato' => $cropTotals['Potato'],
            'Sweet potato' => $cropTotals['Sweet potato'],

            // Actors now = MEMBERS ONLY
            'Farmers' => $actorTotals['Farmers'],
            'Processors' => $actorTotals['Processors'],
            'Aggregators' => $actorTotals['Aggregators'],
            'Transporters' => $actorTotals['Transporters'],

            'Traders' => $traders['total'],

            'New establishment' => $new,
            'Old establishment' => $old,
        ];
    }


    public function getActorGenderDisaggregation()
    {
        $actors = ['Farmers', 'Processors', 'Aggregators', 'Transporters'];

        $results = [];

        foreach ($actors as $actor) {

            $builder = $this->builder()
                ->where('type', $actor)
                ->where('type', '!=', 'Traders');

            $male_members = 0;
            $female_members = 0;
            $male_employees = 0;
            $female_employees = 0;
            $youth = 0;
            $not_youth = 0;
            $male = 0;
            $female = 0;

            foreach ($builder->get() as $row) {

                // MEMBERS
                $female_members +=
                    $row->mem_female_18_35 +
                    $row->mem_female_35_plus;



                $male_members +=
                    $row->mem_male_18_35 +
                    $row->mem_male_35_plus;



                // EMPLOYEES (formal + informal)
                $female_employees +=
                    $row->emp_formal_female_18_35 +
                    $row->emp_formal_female_35_plus +
                    $row->emp_informal_female_18_35 +
                    $row->emp_informal_female_35_plus;

                $male_employees +=
                    $row->emp_formal_male_18_35 +
                    $row->emp_formal_male_35_plus +
                    $row->emp_informal_male_18_35 +
                    $row->emp_informal_male_35_plus;

                $youth += $row->mem_female_18_35 + $row->mem_male_18_35 + $row->emp_formal_female_18_35 + $row->emp_formal_male_18_35 + $row->emp_informal_female_18_35 + $row->emp_informal_male_18_35;
                $not_youth += $row->mem_female_35_plus + $row->mem_male_35_plus + $row->emp_formal_female_35_plus + $row->emp_formal_male_35_plus + $row->emp_informal_female_35_plus + $row->emp_informal_male_35_plus;
            }

            $results[$actor] = [
                'male' => $male_employees + $male_members,
                'female' => $female_employees + $female_members,
                'male_members' => $male_members,
                'female_members' => $female_members,
                'male_employees' => $male_employees,
                'female_employees' => $female_employees,
                'youth' => $youth,
                'not_youth' => $not_youth
            ];
        }

        return $results;
    }
}
