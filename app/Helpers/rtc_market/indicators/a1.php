<?php

namespace App\Helpers\rtc_market\indicators;

use App\Livewire\Internal\Cip\Submissions;
use App\Models\HouseholdRtcConsumption;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class A1
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

        if ($this->reporting_period || $this->financial_year) {
            if ($this->reporting_period) {
                dd($this->reporting_period);

            }



            // $query->where(function ($query) {
            //     if ($this->reporting_period) {
            //         $filterUuids = $query->pluck('uuid')->unique()->toArray();
            //         $submissions = Submission::whereIn('batch_no', $filterUuids)->where('period_id', $this->reporting_period)->pluck('batch_no');
            //         $query->whereIn('uuid', $submissions->toArray());

            //     }
            //     if ($this->financial_year) {
            //         $filterUuids = $query->pluck('uuid')->unique()->toArray();
            //         $submissions = Submission::whereIn('batch_no', $filterUuids)->pluck('period_id')->unique();
            //         $periods = SubmissionPeriod::whereIn('id', $submissions->toArray())->where('financial_year_id', $this->financial_year)->pluck('id');
            //         $submissions = Submission::where('period_id', $periods->toArray())->pluck('batch_no');
            //         $query->whereIn('uuid', $submissions->toArray());
            //     }
            // });






        }

        return $query;

    }

    public function findTotal()
    {
        return $this->builder()->count();
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

    public function getDisaggregations()
    {
        $gender = $this->findGender();
        $age = $this->findAge();
        $actorType = $this->findActorType();
        $crop = $this->findByCrop();

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
        ];

    }
}