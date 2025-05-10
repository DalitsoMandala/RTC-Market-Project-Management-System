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

    public function builderFarmer(): Builder
    {
        return $this->applyFilters(RtcProductionFarmer::query()->where('status', 'approved'));
    }

    public function builderProcessor(): Builder
    {
        return $this->applyFilters(RtcProductionProcessor::query()->where('status', 'approved'));
    }

    // Example of chunking data in `findTotalFarmerEmployees` method
    public function findTotalEmployees()
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
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];
    }

    public function findTotalMembers()
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
        $totalEmployees = $this->findTotalEmployees();

        return $this->builder()->count() + $totalEmployees['Total'];
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

        $totalActor = collect(['Total' => 0, 'Farmers' => 0, 'Processors' => 0, 'Traders' => 0]);

        $this->builder()->chunk(100, function ($data) use (&$totalActor) {

            foreach ($data as $model) {

                $totalActor['Total'] += 1;
                $totalActor['Farmers'] += $model->type == 'Farmers' ? 1 : 0;
                $totalActor['Processors'] += $model->type == 'Processors' ? 1 : 0;
                $totalActor['Traders'] += $model->type == 'Traders' ? 1 : 0;
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

    public function getEstablishment()
    {
        return $this->builder()->select([
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
        $members = $this->findTotalMembers();
        $actorType = $this->findActorType();
        $crop = $this->findByCrop();
        $totalEmployees = $this->findTotalEmployees();
        $totalOldEstablishment = $this->getEstablishment()['Old'];
        $totalNewEstablishment = $this->getEstablishment()['New'];

        return collect([
            'Total' => $this->findTotal(),
            'Female' => $members['totalFemale'],
            'Male' => $members['totalMale'],
            'Youth (18-35 yrs)' => $members['totalYouth'],
            'Not youth (35yrs+)' => $members['totalAdult'],
            'Farmers' => $actorType['Farmers'],
            'Processors' => $actorType['Processors'],
            'Traders' => $actorType['Traders'],
            'Cassava' => $crop['cassava'],
            'Potato' => $crop['potato'],
            'Sweet potato' => $crop['sweet_potato'],
            'Employees on RTC establishment' => $totalEmployees['Total'],
            'New establishment' => $totalNewEstablishment,
            'Old establishment' => $totalOldEstablishment,
        ])->map(function ($value) {
            return is_numeric($value) && floor($value) != $value
                ? (float) $value  // keep decimal if exists
                : (int) $value;   // force to int if whole number
        })->toArray();
    }
}