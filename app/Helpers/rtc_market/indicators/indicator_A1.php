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


        return $this->applyFilters(HouseholdRtcConsumption::query()->where('status', 'approved'));
    }

    public function builderFarmer(): Builder
    {
        return $this->applyFilters(RtcProductionFarmer::query()->where('status', 'approved'));
    }

    public function builderProcessor(): Builder
    {
        return $this->applyFilters(RtcProductionProcessor::query()->where('status', 'approved'));
    }

    // Example of chunking data in `findTotalFarmerEmployees` method
    public function findTotalFarmerEmployees()
    {
        $totalEmpFormal = 0;
        $totalEmpInFormal = 0;

        $this->builderFarmer()->chunk(100, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
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
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];
    }

    // Similarly, you can chunk data in `findTotalProcessorEmployees`
    public function findTotalProcessorEmployees()
    {
        $totalEmpFormal = 0;
        $totalEmpInFormal = 0;

        $this->builderProcessor()->chunk(100, function ($data) use (&$totalEmpFormal, &$totalEmpInFormal) {
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
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];
    }

    // Continue using chunking for other large data-fetching functions as needed

    public function findTotal()
    {
        $totalFarmerEmployees = $this->findTotalFarmerEmployees();
        $totalProcessorEmployees = $this->findTotalProcessorEmployees();
        return $this->builder()->count() + $totalFarmerEmployees['Total'] + $totalProcessorEmployees['Total'];
    }

    public function findGender()
    {
        $totalGender = ['Total' => 0, 'MaleCount' => 0, 'FemaleCount' => 0];

        $this->builder()->chunk(100, function ($data) use (&$totalGender) {
            foreach ($data as $model) {
                $totalGender['Total'] += 1;
                $totalGender['MaleCount'] += $model->sex == 'Male' ? 1 : 0;
                $totalGender['FemaleCount'] += $model->sex == 'Female' ? 1 : 0;
            }
        });

        return $totalGender;
    }
    public function findAge(): Collection
    {
        $totalAge = collect(['Total' => 0, 'youth' => 0, 'not_youth' => 0]);

        $this->builder()->chunk(100, function ($data) use (&$totalAge) {
            foreach ($data as $model) {
                $totalAge['Total'] += 1;
                $totalAge['youth'] += $model->age_group == 'Youth' ? 1 : 0;
                $totalAge['not_youth'] += $model->age_group == 'Not youth' ? 1 : 0;
            }
        });

        return $totalAge;
    }
    public function findActorType()
    {

        return $this->countActor()->toArray();
    }

    public function countCrop(): Collection
    {
        $totalCrop = collect(['potato' => 0, 'cassava' => 0, 'sweet_potato' => 0]);

        $this->builder()->chunk(100, function ($data) use (&$totalCrop) {
            foreach ($data as $model) {
                if ($model->enterprise == 'Potato') {
                    $totalCrop['potato'] += 1;
                } elseif ($model->enterprise == 'Cassava') {
                    $totalCrop['cassava'] += 1;
                } elseif ($model->enterprise == 'Sweet potato') {
                    $totalCrop['sweet_potato'] += 1;
                }
            }
        });

        return $totalCrop;
    }


    public function countActor(): Collection
    {
        $totalActor = collect(['Total' => 0, 'farmer' => 0, 'processor' => 0, 'trader' => 0]);

        $this->builder()->chunk(100, function ($data) use (&$totalActor) {
            foreach ($data as $model) {
                $totalActor['Total'] += 1;
                $totalActor['farmer'] += $model->actor_type == 'Farmer' ? 1 : 0;
                $totalActor['processor'] += $model->actor_type == 'Processor' ? 1 : 0;
                $totalActor['trader'] += $model->actor_type == 'Trader' ? 1 : 0;
            }
        });

        return $totalActor;
    }
    public function findByCrop()
    {

        return $this->countCrop()->toArray();
    }

    public function RtcActorByCrop($actor)
    {
        return $this->countCrop()->where('actor_type', $actor)->first()->toArray();
    }

    public function RtcActorBySex($sex)
    {
        return $this->countActor()->where('sex', $sex)->first()->toArray();
    }
    public function RtcActorByAge($age)
    {
        return $this->countActor()->where('age_group', $age)->first()->toArray();
    }

    public function getEstablishmentFarmers()
    {
        return $this->builderFarmer()->select([
            DB::raw('COUNT(*) AS Total'),
            DB::raw('SUM(CASE WHEN establishment_status = \'New\' THEN 1 ELSE 0 END) AS New'),
            DB::raw('SUM(CASE WHEN establishment_status = \'Old\' THEN 1 ELSE 0 END) AS Old'),

        ])->first()->toArray();
    }

    public function getEstablishmentProcessors()
    {
        return $this->builderProcessor()->select([
            DB::raw('COUNT(*) AS Total'),
            DB::raw('SUM(CASE WHEN establishment_status = \'New\' THEN 1 ELSE 0 END) AS New'),
            DB::raw('SUM(CASE WHEN establishment_status = \'Old\' THEN 1 ELSE 0 END) AS Old'),

        ])->first()->toArray();
    }


    public function getDisaggregations()
    {
        $gender = $this->findGender();
        $age = $this->findAge();
        $actorType = $this->findActorType();
        $crop = $this->findByCrop();


        $totalFarmerEmployees = $this->findTotalFarmerEmployees();

        $totalProcessorEmployees = $this->findTotalProcessorEmployees();
        $totalEmployees = $totalFarmerEmployees['Total'] + $totalProcessorEmployees['Total'];
        $totalOldEstablishment = $this->getEstablishmentFarmers()['Old'] + $this->getEstablishmentProcessors()['Old'];
        $totalNewEstablishment = $this->getEstablishmentFarmers()['New'] + $this->getEstablishmentProcessors()['New'];

        return [
            'Total' => $this->findTotal(),
            'Female' => (int) $gender['FemaleCount'],
            'Male' => (int) $gender['MaleCount'],
            'Youth (18-35 yrs)' => (int) $age['youth'],
            'Not youth (35yrs+)' => (int) $age['not_youth'],
            'Farmers' => (int) $actorType['farmer'],
            'Processors' => (int) $actorType['processor'],
            'Traders' => (int) $actorType['trader'],
            'Cassava' => (int) $crop['cassava'],
            'Potato' => (int) $crop['potato'],
            'Sweet potato' => (int) $crop['sweet_potato'],
            'Employees on RTC establishment' => $totalEmployees,
            'New establishment' => $totalNewEstablishment,
            'Old establishment' => $totalOldEstablishment,
        ];
    }
}
