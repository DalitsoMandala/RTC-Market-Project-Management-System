<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class A1
{

    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;

    public function __construct($start_date = null, $end_date = null)
    {

        $this->start_date = $start_date;
        $this->end_date = $end_date;

    }
    public function builder(): Builder
    {

        $query = HouseholdRtcConsumption::query();

        if ($this->start_date || $this->end_date) {

            $query->where(function ($query) {
                if ($this->start_date) {
                    $query->where('created_at', '>=', $this->start_date);
                }
                if ($this->end_date) {
                    $query->where('created_at', '<=', $this->end_date);
                }
            });
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

        return [
            'Total' => (int) $this->findTotal(),
            'Female' => (int) $this->findGender()['FemaleCount'],
            'Male' => (int) $this->findGender()['MaleCount'],
            'Youth (18-35 yrs)' => (int) $this->findAge()['youth'],
            'Not youth (35yrs+)' => (int) $this->findAge()['not_youth'],
            'Farmers' => (int) $this->findActorType()['farmer'],
            'Processors' => (int) $this->findActorType()['processor'],
            'Traders' => (int) $this->findActorType()['trader'],
            'Cassava' => (int) $this->findByCrop()['cassava'],
            'Potato' => (int) $this->findByCrop()['potato'],
            'Sweet potato' => (int) $this->findByCrop()['sweet_potato'],
        ];

    }
}
