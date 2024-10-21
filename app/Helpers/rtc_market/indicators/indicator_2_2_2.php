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
    public function builderFarmer($crop = null): Builder
    {


        $query = RtcProductionFarmer::query()->where('status', 'approved')
            ->with(['basicSeed', 'certifiedSeed']);

        // Check if both reporting period and financial year are set
        if ($this->reporting_period || $this->financial_year) {
            // Apply filter for reporting period if it's set
            if ($this->reporting_period) {
                $query->where('period_month_id', $this->reporting_period);
            }

            // Apply filter for financial year if it's set
            if ($this->financial_year) {
                $query->where('financial_year_id', $this->financial_year);
            }

            // If no data is found, return an empty result
            if (!$query->exists()) {
                $query->whereIn('id', []); // Empty result filter
            }
        }

        // Filter by organization if set
        if ($this->organisation_id) {
            $query->where('organisation_id', $this->organisation_id);
        }


        if ($crop) {

            $query->where('enterprise', $crop);
            return $query;
        }

        return $query;
    }


    public function getBasicSeed($cropQuery = null)
    {
        $totalArea = 0;

        // Use the provided crop query or the default builderFarmer query
        $query = $cropQuery ?? $this->builderFarmer()->where('group', 'Early generation seed producer');

        // Process the query in chunks
        $query->chunk(100, function ($farmers) use (&$totalArea) {
            // Pluck, flatten, and sum the area from the basicSeed relationship
            $basicSeedArea = $farmers->pluck('basicSeed')->flatten()->sum('area');
            $totalArea += $basicSeedArea;
        });

        return $totalArea;
    }




    public function getCertifiedSeed($cropQuery = null)
    {
        $totalArea = 0;

        // Use the provided crop query or the default builderFarmer query
        $query = $cropQuery ?? $this->builderFarmer()->where('group', 'Seed multiplier');

        $query->chunk(100, function ($farmers) use (&$totalArea) {
            $certifiedSeedArea = $farmers->pluck('certifiedSeed')->flatten()->sum('area');
            $totalArea += $certifiedSeedArea;
        });

        return $totalArea;
    }


    public function getCrop()
    {
        $queryCassava = $this->builderFarmer('Cassava')->where('group', 'Seed multiplier');
        $queryPotato = $this->builderFarmer('Potato')->where('group', 'Seed multiplier');
        $querySwPotato = $this->builderFarmer('Sweet potato')->where('group', 'Seed multiplier');




        return [

            'cassava' => $queryCassava->count(),
            'potato' => $queryPotato->count(),
            'sweet_potato' => $querySwPotato->count(),
        ];
    }


    public function getDisaggregations()
    {
        $cropData = $this->getCrop(); // Store crop data to avoid redundant calls
        $total = $this->getBasicSeed() + $this->getCertifiedSeed();

        return [
            'Total' => $total,
            'Cassava' => $cropData['cassava'],
            'Potato' => $cropData['potato'],
            'Sweet potato' => $cropData['sweet_potato'],
            'Basic' => $this->getBasicSeed(),
            'Certified' => $this->getCertifiedSeed(),
        ];
    }
}
