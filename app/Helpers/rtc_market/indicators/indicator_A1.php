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

    protected $target_year_id;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;
    }



    public function builder(): Builder
    {


        return $this->applyFilters(Recruitment::query()->where('status', 'approved'));
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

    public function getDisaggregations()
    {
        $actorsData = $this->findActorTypeBreakdown();
        $cropsData = $this->findCropBreakdown();

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

        // Helper function to safely sum group fields

        return collect([
            'Total' => $actorTotals['totalEmployeeFormal'] + $actorTotals['totalEmployeeInFormal'] + $actorTotals['totalFemale'] + $actorTotals['totalMale'],
            'Female' => $actorTotals['totalFemale'],
            'Male' => $actorTotals['totalMale'],
            'Youth (18-35 yrs)' => $actorTotals['totalYouth'],
            'Not youth (35yrs+)' => $actorTotals['totalAdult'],
            'Farmers' => $this->sumGroup($actorsData, 'Farmers', ['totalEmployeeFormal', 'totalEmployeeInFormal', 'totalFemale', 'totalMale']),
            'Processors' => $this->sumGroup($actorsData, 'Processors', ['totalEmployeeFormal', 'totalEmployeeInFormal', 'totalFemale', 'totalMale']),
            'Traders' => $this->sumGroup($actorsData, 'Traders', ['totalEmployeeFormal', 'totalEmployeeInFormal', 'totalFemale', 'totalMale']),
            'Cassava' => $this->sumGroup($cropsData, 'Cassava', ['totalEmployeeFormal', 'totalEmployeeInFormal', 'totalFemale', 'totalMale']),
            'Potato' => $this->sumGroup($cropsData, 'Potato', ['totalEmployeeFormal', 'totalEmployeeInFormal', 'totalFemale', 'totalMale']),
            'Sweet potato' => $this->sumGroup($cropsData, 'Sweet potato', ['totalEmployeeFormal', 'totalEmployeeInFormal', 'totalFemale', 'totalMale']),
            'Employees on RTC establishment' => $actorTotals['totalEmployeeFormal'] + $actorTotals['totalEmployeeInFormal'],
            'New establishment' => $actorTotals['new_establishments'],
            'Old establishment' => $actorTotals['old_establishments'],
        ])->map(function ($value) {
            return is_numeric($value) && floor($value) != $value
                ? (float) $value
                : (int) $value;
        })->toArray();
    }
}