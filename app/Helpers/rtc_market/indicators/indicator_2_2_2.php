<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\RpmFarmerFollowUp;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_2
{
    use FilterableQuery;
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
        $query = RtcProductionFarmer::query()
            ->where('status', 'approved')
            ->with([
                'basicSeed',
                'certifiedSeed'
            ]);



        // Filter by crop type if provided
        if ($crop) {
            $query->where('enterprise', $crop);
        }

        return $this->applyFilters($query);
    }

    public function getBasicSeed($crop = null)
    {
        $totalArea = 0;

        // Use the builderFarmer query with specified crop and filter by group
        $query = $this->builderFarmer($crop);

        // Process the query in chunks to avoid memory issues
        $query->chunk(100, function ($farmers) use (&$totalArea) {
            // Calculate the area for basic seeds
            $basicSeedArea = $farmers->pluck('basicSeed')->flatten()->sum('area');
            $totalArea += $basicSeedArea;
        });

        return $totalArea;
    }

    public function getCertifiedSeed($crop = null)
    {
        $totalArea = 0;

        // Use the builderFarmer query with specified crop and filter by group
        $query = $this->builderFarmer($crop);

        $query->chunk(100, function ($farmers) use (&$totalArea) {
            // Calculate the area for certified seeds
            $certifiedSeedArea = $farmers->pluck('certifiedSeed')->flatten()->sum('area');
            $totalArea += $certifiedSeedArea;
        });

        return $totalArea;
    }

    public function getCrop()
    {
        // Calculate basic and certified seed areas for each crop type
        $queryCassavaBasic = $this->getBasicSeed('Cassava');
        $queryCassavaCertified = $this->getCertifiedSeed('Cassava');
        $queryPotatoBasic = $this->getBasicSeed('Potato');
        $queryPotatoCertified = $this->getCertifiedSeed('Potato');
        $querySwPotatoBasic = $this->getBasicSeed('Sweet potato');
        $querySwPotatoCertified = $this->getCertifiedSeed('Sweet potato');

        // Aggregate areas by crop type
        return [
            'cassava' => $queryCassavaBasic + $queryCassavaCertified,
            'potato' => $queryPotatoBasic + $queryPotatoCertified,
            'sweet_potato' => $querySwPotatoBasic + $querySwPotatoCertified,
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