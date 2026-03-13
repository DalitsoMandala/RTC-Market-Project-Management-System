<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Recruitment;
use App\Models\RtcConsumption;

class indicator_B4
{
    use FilterableQuery;

    protected $financial_year;
    protected $reporting_period;
    protected $organisation_id;
    protected $enterprise;

    public function __construct($reporting_period=null,$financial_year=null,$organisation_id=null,$enterprise=null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }

    /*
    |--------------------------------------------------------------------------
    | Base Builders
    |--------------------------------------------------------------------------
    */

    public function builder(): Builder
    {
        return $this->applyFilters(
            Recruitment::query()->where('status','approved')
        );
    }

    public function builderSchool(): Builder
    {
        return $this->applyHouseHoldFilters(
            RtcConsumption::query()
                ->where('status','approved')
                ->where('entity_type','School')
        );
    }

    public function builderHousehold(): Builder
    {
        return $this->applyHouseHoldFilters(
            RtcConsumption::query()->where('status','approved')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Main Aggregation Query
    |--------------------------------------------------------------------------
    */

    public function getMainGroup($type=null,$enterprise=null,$estType=null): Builder
    {
        $query = $this->builder();

        if ($type) {
            $query->where('type',$type);
        }

        if ($enterprise) {
            $query->where('enterprise',$enterprise);
        }

        if ($estType) {
            $query->where('establishment_status',$estType);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL MEMBERS + EMPLOYEES (SQL AGGREGATION)
    |--------------------------------------------------------------------------
    */

    public function getTotalSum($type=null,$enterprise=null,$estType=null)
    {
        $result = $this->getMainGroup($type,$enterprise,$estType)
            ->selectRaw('
                SUM(mem_female_18_35 + mem_female_35_plus) as female_members,
                SUM(mem_male_18_35 + mem_male_35_plus) as male_members,

                SUM(emp_formal_female_18_35 + emp_formal_female_35_plus +
                    emp_informal_female_18_35 + emp_informal_female_35_plus) as female_employees,

                SUM(emp_formal_male_18_35 + emp_formal_male_35_plus +
                    emp_informal_male_18_35 + emp_informal_male_35_plus) as male_employees,

                SUM(mem_female_18_35 + mem_male_18_35 +
                    emp_formal_female_18_35 + emp_formal_male_18_35 +
                    emp_informal_female_18_35 + emp_informal_male_18_35) as youth,

                SUM(mem_female_35_plus + mem_male_35_plus +
                    emp_formal_female_35_plus + emp_formal_male_35_plus +
                    emp_informal_female_35_plus + emp_informal_male_35_plus) as adults
            ')
            ->first();

        $members = $result->female_members + $result->male_members;
        $employees = $result->female_employees + $result->male_employees;

        return [
            'members' => $members,
            'employees' => $employees,
            'female' => $result->female_members + $result->female_employees,
            'male' => $result->male_members + $result->male_employees,
            'youth' => $result->youth,
            'not_youth' => $result->adults
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SCHOOL BENEFICIARIES
    |--------------------------------------------------------------------------
    */

    public function getTotalSchool($enterprise=null)
    {
        $builder = $this->builderSchool();

        $enterpriseColumns = [
            'Sweet potato' => 'crop_sweet_potato',
            'Potato' => 'crop_potato',
            'Cassava' => 'crop_cassava',
        ];

        if ($enterprise && isset($enterpriseColumns[$enterprise])) {
            $builder->where($enterpriseColumns[$enterprise],true);
        }

        return [
            'male' => $builder->sum('male_count'),
            'female' => $builder->sum('female_count')
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | INDICATOR DISAGGREGATIONS
    |--------------------------------------------------------------------------
    */

    public function getDisaggregations()
    {
        $totals = $this->getTotalSum();

        $households = $this->builderHousehold()->sum('number_of_households');

        $interventions = $this->builderHousehold()
            ->where('entity_type','Nutrition intervention group')
            ->sum('number_of_households');

        $school = $this->getTotalSchool();

        $householdTotal = ($totals['members'] + $totals['employees']) + ($households * 5);
        $interventionTotal = $interventions * 5;
        $schoolTotal = $school['male'] + $school['female'];

        $grandTotal = $householdTotal + $interventionTotal + $schoolTotal;

        return [
            "Total" => $grandTotal,
            "RTC actors and households" => $householdTotal,
            "School feeding beneficiaries" => $schoolTotal,
            "Individuals from households reached with nutrition interventions" => $interventionTotal,
        ];
    }
}
