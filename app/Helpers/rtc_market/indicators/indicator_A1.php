<?php

namespace App\Helpers\rtc_market\indicators;


use App\Livewire\Internal\Cip\Submissions;
use App\Models\HouseholdRtcConsumption;
use App\Models\Organisation;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class indicator_A1
{

    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;

    protected $financial_year, $reporting_period, $project;
    public function __construct($reporting_period = null, $financial_year = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;

    }
    public function builder(): Builder
    {
        $query = HouseholdRtcConsumption::query();

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }

        return $query;
    }

    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query();

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }

        return $query;
    }


    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query();

        if ($this->reporting_period && $this->financial_year) {
            $hasValidBatchUuids = false;

            $query->where(function ($query) use (&$hasValidBatchUuids) {

                $submissionPeriod = SubmissionPeriod::where('month_range_period_id', $this->reporting_period)->where('financial_year_id', $this->financial_year)->pluck('id')->toArray();
                if (!empty($submissionPeriod)) {
                    $batchUuids = Submission::whereIn('period_id', $submissionPeriod)->pluck('batch_no')->toArray();
                    if (!empty($batchUuids)) {
                        $query->orWhereIn('uuid', $batchUuids);
                        $hasValidBatchUuids = true;
                    }
                }



            });

            if (!$hasValidBatchUuids) {
                // No valid batch UUIDs found, return an empty collection
                return $query->whereIn('uuid', []);
            }
        }

        return $query;
    }

    public function findTotalFarmerEmployees()
    {
        $data = $this->builderFarmer()->get();
        $employees = $data->map(function ($model) {

            $json = collect(json_decode($model->number_of_employees, true));

            if ($json->has('formal')) {
                $formal = $json->get('formal');
                $sum = collect($formal)->except('total')->sum();
                $model->empFormalTotal = $sum;
            } else {
                $model->empFormalTotal = 0;
            }



            if ($json->has('informal')) {
                $formal = $json->get('informal');
                $sum = collect($formal)->except('total')->sum();
                $model->empInFormalTotal = $sum;
            } else {
                $model->empInFormalTotal = 0;
            }

            return $model;
        });


        $totalEmpFormal = $employees->sum('empFormalTotal');
        $totalEmpInFormal = $employees->sum('empInFormalTotal');

        return [
            'totalEmpFormal' => $totalEmpFormal,
            'totalEmpInFormal' => $totalEmpInFormal,
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];

    }

    public function findTotalProcessorEmployees()
    {
        $data = $this->builderProcessor()->get();
        $employees = $data->map(function ($model) {

            $json = collect(json_decode($model->number_of_employees, true));



            if ($json->has('formal')) {
                $formal = $json->get('formal');
                $sum = collect($formal)->except('total')->sum();
                $model->empFormalTotal = $sum;
            } else {
                $model->empFormalTotal = 0;
            }



            if ($json->has('informal')) {
                $formal = $json->get('informal');
                $sum = collect($formal)->except('total')->sum();
                $model->empInFormalTotal = $sum;
            } else {
                $model->empInFormalTotal = 0;
            }

            return $model;
        });


        $totalEmpFormal = $employees->sum('empFormalTotal');
        $totalEmpInFormal = $employees->sum('empInFormalTotal');

        return [
            'totalEmpFormal' => $totalEmpFormal,
            'totalEmpInFormal' => $totalEmpInFormal,
            'Total' => $totalEmpFormal + $totalEmpInFormal
        ];

    }

    public function findTotal()
    {
        return $this->builder()->where('actor_name', '!=', null)->count();
    }

    public function findGender()
    {
        return $this->builder()
            ->select([
                DB::raw('COUNT(*) AS Total'),
                DB::raw('SUM(CASE WHEN sex = \'MALE\' THEN 1 ELSE 0 END) AS MaleCount'),
                DB::raw('SUM(CASE WHEN sex = \'FEMALE\' THEN 1 ELSE 0 END) AS FemaleCount'),
            ])

            ->first()->toArray();
    }
    public function findAge()
    {
        return $this->builder()
            ->select([
                DB::raw('COUNT(*) AS Total'),
                DB::raw('SUM(CASE WHEN age_group = \'YOUTH\' THEN 1 ELSE 0 END) AS youth'),
                DB::raw('SUM(CASE WHEN age_group = \'NOT YOUTH\' THEN 1 ELSE 0 END) AS not_youth'),
            ])

            ->first()->toArray();
    }

    public function findActorType()
    {
        return $this->countActor()->first()->toArray();
    }

    public function countCrop()
    {
        return $this->builder()
            ->select([
                DB::raw('SUM(rtc_consumers_potato) as potato'),
                DB::raw('SUM(rtc_consumers_cassava) as cassava'),
                DB::raw('SUM(rtc_consumers_sw_potato) as sweet_potato'),
            ])

        ;
    }

    public function countActor()
    {
        return $this->builder()
            ->select([
                DB::raw('COUNT(*) AS Total'),
                DB::raw('SUM(CASE WHEN actor_type = \'FARMER\' THEN 1 ELSE 0 END) AS farmer'),
                DB::raw('SUM(CASE WHEN actor_type = \'PROCESSOR\' THEN 1 ELSE 0 END) AS processor'),
                DB::raw('SUM(CASE WHEN actor_type = \'TRADER\' THEN 1 ELSE 0 END) AS trader'),
            ])

        ;
    }
    public function findByCrop()
    {
        return $this->countCrop()->first()->toArray();
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
            DB::raw('SUM(CASE WHEN establishment_status = \'NEW\' THEN 1 ELSE 0 END) AS NEW'),
            DB::raw('SUM(CASE WHEN establishment_status = \'OLD\' THEN 1 ELSE 0 END) AS OLD'),

        ])->first()->toArray();

    }

    public function getEstablishmentProcessors()
    {
        return $this->builderProcessor()->select([
            DB::raw('COUNT(*) AS Total'),
            DB::raw('SUM(CASE WHEN establishment_status = \'NEW\' THEN 1 ELSE 0 END) AS NEW'),
            DB::raw('SUM(CASE WHEN establishment_status = \'OLD\' THEN 1 ELSE 0 END) AS OLD'),

        ])->first()->toArray();
    }

    public function getCurrentTargets($financial_year_ids, $organisation_ids)
    {
        $builder = $this->builder()->select([
            'organisation_id',
            DB::raw('COUNT(*) AS Total'),
        ])
            ->where('actor_name', '!=', null)
            ->whereIn('organisation_id', $organisation_ids)
            ->whereIn('financial_year_id', $financial_year_ids)
            ->groupBy('organisation_id');

        $final = $builder->get()->map(function ($query) {
            $model = Organisation::find($query->organisation_id);
            $query->organisation = $model->name;
            return $query;
        });


        return $final->toArray();
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
        $totalOldEstablishment = $this->getEstablishmentFarmers()['OLD'] + $this->getEstablishmentProcessors()['OLD'];
        $totalNewEstablishment = $this->getEstablishmentFarmers()['NEW'] + $this->getEstablishmentProcessors()['NEW'];

        return [
            'Total' => $this->findTotal(),
            'Female' => (float) $gender['FemaleCount'],
            'Male' => (float) $gender['MaleCount'],
            'Youth (18-35 yrs)' => (float) $age['youth'],
            'Not youth (35yrs+)' => (float) $age['not_youth'],
            'Farmers' => (float) $actorType['farmer'],
            'Processors' => (float) $actorType['processor'],
            'Traders' => (float) $actorType['trader'],
            'Cassava' => (float) $crop['cassava'],
            'Potato' => (float) $crop['potato'],
            'Sweet potato' => (float) $crop['sweet_potato'],
            'Employees on RTC establishment' => $totalEmployees,
            'New establishment' => $totalNewEstablishment,
            'Old establishment' => $totalOldEstablishment,
        ];

    }
}