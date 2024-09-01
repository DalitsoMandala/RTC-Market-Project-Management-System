<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\RpmFarmerFollowUp;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_2
{
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $target_year_id;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;

    }
    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query()->where('status', 'approved');

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // NO data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }


        if ($this->organisation_id && $this->target_year_id) {
            $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
            $query = $data;

        } else
            if ($this->organisation_id && $this->target_year_id == null) {
                $data = $query->where('organisation_id', $this->organisation_id);
                $query = $data;

            }

        return $query;
    }

    public function followUpBuilder()
    {

        $farmer = $this->builderFarmer()->pluck('id');

        return RpmFarmerFollowUp::query()->whereIn('rpm_farmer_id', $farmer);
    }

    public function getBasicSeed()
    {
        $query = $this->builderFarmer()->get()->map(function ($item) {
            $data = json_decode($item->area_under_basic_seed_multiplication);
            return $data && isset($data->total) ? $data->total : null;
        });

        $queryFollowup = $this->followUpBuilder()->get()->map(function ($item) {
            $data = json_decode($item->area_under_basic_seed_multiplication);
            return $data && isset($data->total) ? $data->total : null;
        });

        return $query->sum() + $queryFollowup->sum();
    }

    public function getCertifiedSeed()
    {
        $query = $this->builderFarmer()->get()->map(function ($item) {
            $data = json_decode($item->area_under_certified_seed_multiplication);
            return $data && isset($data->total) ? $data->total : null;
        });

        $queryFollowup = $this->followUpBuilder()->get()->map(function ($item) {
            $data = json_decode($item->area_under_certified_seed_multiplication);
            return $data && isset($data->total) ? $data->total : null;
        });



        return $query->sum() + $queryFollowup->sum();
    }

    public function getCrop()
    {
        $queryCassava = $this->builderFarmer()->get()->map(function ($item) {
            $data = json_decode($item->number_of_plantlets_produced);
            return $data && isset($data->cassava) ? $data->cassava : null;
        });
        $queryPotato = $this->builderFarmer()->get()->map(function ($item) {
            $data = json_decode($item->number_of_plantlets_produced);
            return $data && isset($data->potato) ? $data->potato : null;
        });
        $querySwPotato = $this->builderFarmer()->get()->map(function ($item) {
            $data = json_decode($item->number_of_plantlets_produced);
            return $data && isset($data->sweet_potato) ? $data->sweet_potato : null;
        });

        return [

            'cassava' => $queryCassava->sum(),
            'potato' => $queryPotato->sum(),
            'sweet_potato' => $querySwPotato->sum(),
        ];

    }


    public function getDisaggregations()
    {


        return [
            'Total' => $this->getBasicSeed() + $this->getCertifiedSeed(),
            'Cassava' => $this->getCrop()['cassava'],
            'Potato' => $this->getCrop()['potato'],
            'Sweet potato' => $this->getCrop()['sweet_potato'],
            'Basic' => $this->getBasicSeed(),
            'Certified' => $this->getCertifiedSeed(),
        ];
    }
}
