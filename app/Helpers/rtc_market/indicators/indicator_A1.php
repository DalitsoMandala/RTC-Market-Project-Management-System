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
    // Get base totals (already filtered by enterprise if applicable)
    $baseTotals = $this->getTotalSum();

    // Get people counts for each actor type except traders
    $farmersPeople = $this->getTotalSum(type: 'Farmers')['employees'] + $this->getTotalSum(type: 'Farmers')['members'];
    $processorsPeople = $this->getTotalSum(type: 'Processors')['employees'] + $this->getTotalSum(type: 'Processors')['members'];
    $aggregatorsPeople = $this->getTotalSum(type: 'Aggregators')['employees'] + $this->getTotalSum(type: 'Aggregators')['members'];
    $transportersPeople = $this->getTotalSum(type: 'Transporters')['employees'] + $this->getTotalSum(type: 'Transporters')['members'];

    // For traders, only count the number of trader establishments
      $tradersCount = $this->builder()
        ->where('type', 'Traders')
        ->count();

    // Calculate total people across all actor types, excluding traders if you only want people
    $totalPeople = $farmersPeople + $processorsPeople + $aggregatorsPeople + $transportersPeople + $tradersCount;

    // Get crop totals (already filtered by any existing filters)
    $cassavaPeople = $this->getTotalSum(enterprise: 'Cassava')['employees'] + $this->getTotalSum(enterprise: 'Cassava')['members'];
    $potatoPeople = $this->getTotalSum(enterprise: 'Potato')['employees'] + $this->getTotalSum(enterprise: 'Potato')['members'];
    $sweetPotatoPeople = $this->getTotalSum(enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(enterprise: 'Sweet potato')['members'];

    $result = [
        'Total' => $totalPeople,
        'Employees on RTC establishment' => $baseTotals['employees'],
        'Cassava' => $cassavaPeople,
        'Potato' => $potatoPeople,
        'Sweet potato' => $sweetPotatoPeople,
        'Farmers' => $farmersPeople,
        'Traders' => $tradersCount, // Only counting traders
        'Processors' => $processorsPeople,
        'Aggregators' => $aggregatorsPeople,
        'Transporters' => $transportersPeople,
        'New establishment' => $this->getTotalSum(estType: 'New')['employees'] + $this->getTotalSum(estType: 'New')['members'],
        'Old establishment' => $this->getTotalSum(estType: 'Old')['employees'] + $this->getTotalSum(estType: 'Old')['members'],
    ];

    return $result;
}

    //     public function getDisaggregations()
    // {
    //     // Get establishment counts for each actor type
    //     $farmersCount = $this->builder()->where('type', 'Farmers')->count();
    //     $tradersCount = $this->builder()->where('type', 'Traders')->count();
    //     $processorsCount = $this->builder()->where('type', 'Processors')->count();
    //     $aggregatorsCount = $this->builder()->where('type', 'Aggregators')->count();
    //     $transportersCount = $this->builder()->where('type', 'Transporters')->count();

    //     // Calculate total establishments
    //     $totalEstablishments = $farmersCount + $tradersCount + $processorsCount + $aggregatorsCount + $transportersCount;

    //     // Get establishment counts by crop
    //     $cassavaCount = $this->builder()->where('enterprise', 'Cassava')->count();
    //     $potatoCount = $this->builder()->where('enterprise', 'Potato')->count();
    //     $sweetPotatoCount = $this->builder()->where('enterprise', 'Sweet potato')->count();

    //     // Get establishment counts by status
    //     $newCount = $this->builder()->where('establishment_status', 'New')->count();
    //     $oldCount = $this->builder()->where('establishment_status', 'Old')->count();

    //     // Get total employees (this remains a sum of people)
    //     $baseTotals = $this->getTotalSum();

    //     $result = [
    //         'Total' => $totalEstablishments,
    //         'Employees on RTC establishment' => $baseTotals['employees'],
    //         'Cassava' => $cassavaCount,
    //         'Potato' => $potatoCount,
    //         'Sweet potato' => $sweetPotatoCount,
    //         'Farmers' => $farmersCount,
    //         'Traders' => $tradersCount,
    //         'Processors' => $processorsCount,
    //         'Aggregators' => $aggregatorsCount,
    //         'Transporters' => $transportersCount,
    //         'New establishment' => $newCount,
    //         'Old establishment' => $oldCount,
    //     ];

    //     return $result;
    // }
    //  public function getDisaggregations()
    // {
    //     // Get base totals
    //     $baseTotals = $this->getTotalSum();

    //     // Get people counts for actor types that sum individuals
    //     $farmersPeople = $this->getTotalSum(type: 'Farmers')['employees'] + $this->getTotalSum(type: 'Farmers')['members'];
    //     $processorsPeople = $this->getTotalSum(type: 'Processors')['employees'] + $this->getTotalSum(type: 'Processors')['members'];
    //     $aggregatorsPeople = $this->getTotalSum(type: 'Aggregators')['employees'] + $this->getTotalSum(type: 'Aggregators')['members'];
    //     $transportersPeople = $this->getTotalSum(type: 'Transporters')['employees'] + $this->getTotalSum(type: 'Transporters')['members'];

    //     // Get count of traders (number of establishments)
    //     $tradersCount = $this->builder()
    //         ->where('type', 'Traders')
    //         ->count();

    //     // Calculate total
    //     $total = $farmersPeople + $tradersCount + $processorsPeople + $aggregatorsPeople + $transportersPeople;

    //     // For crop totals, we need to calculate them consistently
    //     // For each crop, sum: Farmers(people) + Traders(count) + Processors(people) + Aggregators(people) + Transporters(people)

    //     $cassavaFarmers = $this->getTotalSum(type: 'Farmers', enterprise: 'Cassava')['employees'] + $this->getTotalSum(type: 'Farmers', enterprise: 'Cassava')['members'];
    //     $cassavaTraders = $this->builder()->where('type', 'Traders')->where('enterprise', 'Cassava')->count();
    //     $cassavaProcessors = $this->getTotalSum(type: 'Processors', enterprise: 'Cassava')['employees'] + $this->getTotalSum(type: 'Processors', enterprise: 'Cassava')['members'];
    //     $cassavaAggregators = $this->getTotalSum(type: 'Aggregators', enterprise: 'Cassava')['employees'] + $this->getTotalSum(type: 'Aggregators', enterprise: 'Cassava')['members'];
    //     $cassavaTransporters = $this->getTotalSum(type: 'Transporters', enterprise: 'Cassava')['employees'] + $this->getTotalSum(type: 'Transporters', enterprise: 'Cassava')['members'];

    //     $cassavaTotal = $cassavaFarmers + $cassavaTraders + $cassavaProcessors + $cassavaAggregators + $cassavaTransporters;

    //     // Similar for Potato and Sweet potato
    //     $potatoFarmers = $this->getTotalSum(type: 'Farmers', enterprise: 'Potato')['employees'] + $this->getTotalSum(type: 'Farmers', enterprise: 'Potato')['members'];
    //     $potatoTraders = $this->builder()->where('type', 'Traders')->where('enterprise', 'Potato')->count();
    //     $potatoProcessors = $this->getTotalSum(type: 'Processors', enterprise: 'Potato')['employees'] + $this->getTotalSum(type: 'Processors', enterprise: 'Potato')['members'];
    //     $potatoAggregators = $this->getTotalSum(type: 'Aggregators', enterprise: 'Potato')['employees'] + $this->getTotalSum(type: 'Aggregators', enterprise: 'Potato')['members'];
    //     $potatoTransporters = $this->getTotalSum(type: 'Transporters', enterprise: 'Potato')['employees'] + $this->getTotalSum(type: 'Transporters', enterprise: 'Potato')['members'];

    //     $potatoTotal = $potatoFarmers + $potatoTraders + $potatoProcessors + $potatoAggregators + $potatoTransporters;

    //     $sweetPotatoFarmers = $this->getTotalSum(type: 'Farmers', enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(type: 'Farmers', enterprise: 'Sweet potato')['members'];
    //     $sweetPotatoTraders = $this->builder()->where('type', 'Traders')->where('enterprise', 'Sweet potato')->count();
    //     $sweetPotatoProcessors = $this->getTotalSum(type: 'Processors', enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(type: 'Processors', enterprise: 'Sweet potato')['members'];
    //     $sweetPotatoAggregators = $this->getTotalSum(type: 'Aggregators', enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(type: 'Aggregators', enterprise: 'Sweet potato')['members'];
    //     $sweetPotatoTransporters = $this->getTotalSum(type: 'Transporters', enterprise: 'Sweet potato')['employees'] + $this->getTotalSum(type: 'Transporters', enterprise: 'Sweet potato')['members'];

    //     $sweetPotatoTotal = $sweetPotatoFarmers + $sweetPotatoTraders + $sweetPotatoProcessors + $sweetPotatoAggregators + $sweetPotatoTransporters;

    //     $result = [
    //         'Total' => $total,
    //         'Employees on RTC establishment' => $baseTotals['employees'],
    //         'Cassava' => $cassavaTotal,
    //         'Potato' => $potatoTotal,
    //         'Sweet potato' => $sweetPotatoTotal,
    //         'Farmers' => $farmersPeople,
    //         'Traders' => $tradersCount,
    //         'Processors' => $processorsPeople,
    //         'Aggregators' => $aggregatorsPeople,
    //         'Transporters' => $transportersPeople,
    //         'New establishment' => $this->getTotalSum(estType: 'New')['employees'] + $this->getTotalSum(estType: 'New')['members'],
    //         'Old establishment' => $this->getTotalSum(estType: 'Old')['employees'] + $this->getTotalSum(estType: 'Old')['members'],
    //     ];

    //     return $result;
    // }
}
