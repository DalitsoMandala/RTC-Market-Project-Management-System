<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;


use App\Models\Submission;
use App\Models\Organisation;

use App\Models\SubmissionPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;
use App\Livewire\Internal\Cip\Submissions;
use App\Models\Recruitment;

class indicator_A1
{
    use FilterableQuery;

    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;


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



    public function builder(): Builder
    {
        $query = Recruitment::query()->where('status', 'approved');

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

    public function sumGroup($data, $groupKey, $fields)
    {
        $sum = 0;
        if (isset($data[$groupKey])) {
            foreach ($fields as $field) {
                $sum += $data[$groupKey][$field];
            }
        }
        return $sum;
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
        // Base totals without filters
        $baseTotals = $this->getTotalSum();

        // Initialize result with base totals
        $result = [
            'Total' => $baseTotals['employees'] + $baseTotals['members'],
            'Male' => $baseTotals['male'],
            'Female' => $baseTotals['female'],
            'Youth (18-35 yrs)' => $baseTotals['youth'],
            'Not youth (35yrs+)' => $baseTotals['not_youth'],
            'Employees on RTC establishment' => $baseTotals['employees'],
        ];

        // Only calculate these if no enterprise filter is set
        if (!$this->enterprise) {
            $result = array_merge($result, [
            'Cassava' => $this->getTotalSum(enterprise: 'Cassava')['employees'] + $this->getTotalSum(enterprise: 'Cassava')['members'],
            'Potato' => $this->getTotalSum(enterprise: 'Potato')['employees'] + $this->getTotalSum(enterprise: 'Potato')['members'],
            'Sweet potato' => $this->getTotalSum(enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(enterprise: 'Sweet potato')['members'],
            ]);
        } else {
            // Add filtered enterprise with original key name
            $result[$this->enterprise] = $baseTotals['employees'] + $baseTotals['members'];
        }

        // Always include these categories
        $result = array_merge($result, [
            'Farmers' => $this->getTotalSum(type: 'Farmers')['employees'] + $this->getTotalSum(type: 'Farmers')['members'],
            'Traders' => $this->getTotalSum(type: 'Traders')['employees'] + $this->getTotalSum(type: 'Traders')['members'],
            'Processors' => $this->getTotalSum(type: 'Processors')['employees'] + $this->getTotalSum(type: 'Processors')['members'],
            'New establishment' => $this->getTotalSum(estType: 'New')['employees'] + $this->getTotalSum(estType: 'New')['members'],
            'Old establishment' => $this->getTotalSum(estType: 'Old')['employees'] + $this->getTotalSum(estType: 'Old')['members'],
        ]);

        return $result;
    }
    // public function getDisaggregations()
    // {



    //     return [
    //         'Total' => $this->getTotalSum()['employees'] + $this->getTotalSum()['members'],
    //         'Cassava' => $this->getTotalSum(enterprise: 'Cassava')['employees'] + $this->getTotalSum(enterprise: 'Cassava')['members'],
    //         'Potato' => $this->getTotalSum(enterprise: 'Potato')['employees'] + $this->getTotalSum(enterprise: 'Potato')['members'],
    //         'Sweet potato' => $this->getTotalSum(enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(enterprise: 'Sweet potato')['members'],
    //         'Farmers' => $this->getTotalSum(type: 'Farmers')['employees'] + $this->getTotalSum(type: 'Farmers')['members'],
    //         'Traders' => $this->getTotalSum(type: 'Traders')['employees'] + $this->getTotalSum(type: 'Traders')['members'],
    //         'Processors' => $this->getTotalSum(type: 'Processors')['employees'] + $this->getTotalSum(type: 'Processors')['members'],
    //         'Male' => $this->getTotalSum()['male'],
    //         'Female' => $this->getTotalSum()['female'],
    //         'Youth (18-35 yrs)' => $this->getTotalSum()['youth'],
    //         'Not youth (35yrs+)' => $this->getTotalSum()['not_youth'],
    //         'Employees on RTC establishment' => $this->getTotalSum()['employees'],
    //         'New establishment' => $this->getTotalSum(estType: 'New')['employees'] + $this->getTotalSum(estType: 'New')['members'],
    //         'Old establishment' => $this->getTotalSum(estType: 'Old')['employees'] + $this->getTotalSum(estType: 'Old')['members'],
    //     ];
    // }
}
