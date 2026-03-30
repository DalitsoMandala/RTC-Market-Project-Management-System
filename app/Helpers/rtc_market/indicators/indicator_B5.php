<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use Illuminate\Support\Facades\Log;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use App\Models\RpmFarmerInterMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RtcProductionProcessor;
use App\Models\HouseholdRtcConsumption;
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmProcessorConcAgreement;
use Illuminate\Database\Eloquent\Builder;


class indicator_B5
{
    use FilterableQuery;
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


    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query();

        return $this->applyFilters($query);
    }
    public function Processorbuilder(): Builder
    {

        $query = RtcProductionProcessor::query();

        return $this->applyFilters($query);
    }



protected $allowedEnterprises = ['Cassava', 'Potato', 'Sweet potato'];

public function findCropCount()
{
    $result = [];

    foreach ($this->allowedEnterprises as $enterprise) {
        // We calculate the sum of all three categories for this specific crop
        $farmerTotal = $this->builderFarmer()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_cuttings') +
                       $this->builderFarmer()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_seed') +
                       $this->builderFarmer()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_produce');

        $processorTotal = $this->Processorbuilder()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_cuttings') +
                          $this->Processorbuilder()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_seed') +
                          $this->Processorbuilder()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_produce');

        $result[strtolower(str_replace(' ', '_', $enterprise))] = $farmerTotal + $processorTotal;
    }

    return $result;
}

public function findTypeCount()
{
    $results = ['Cuttings' => 0, 'Seed' => 0, 'Produce' => 0];

    // Determine which enterprises to filter by
    // If one is selected, use it. Otherwise, use the standard list.
    $filterList = $this->enterprise ? [$this->enterprise] : $this->allowedEnterprises;

    foreach ($filterList as $enterprise) {
        // Cuttings
        $results['Cuttings'] += $this->builderFarmer()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_cuttings');
        $results['Cuttings'] += $this->Processorbuilder()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_cuttings');

        // Seed
        $results['Seed'] += $this->builderFarmer()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_seed');
        $results['Seed'] += $this->Processorbuilder()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_seed');

        // Produce
        $results['Produce'] += $this->builderFarmer()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_produce');
        $results['Produce'] += $this->Processorbuilder()->where('enterprise', $enterprise)->sum('total_vol_production_previous_season_produce');
    }

    return $results;
}







    public function getDisaggregations()
    {
        $crop = $this->findCropCount();

        $seed = $this->findTypeCount();
        // Define all possible crops with default 0 values

        $allCrops = [
            'Cassava'      => round($crop['cassava'] ?? 0, 2),
            'Sweet potato' => round($crop['sweet_potato'] ?? 0, 2),
            'Potato'       => round($crop['potato'] ?? 0, 2),
        ];

        $allSeeds = [
            'Cuttings' => round($seed['Cuttings'] ?? 0, 2),
            'Seed' => round($seed['Seed'] ?? 0, 2),
            'Produce' => round($seed['Produce'] ?? 0, 2),
        ];


        return [
            'Total (% Percentage)' => 0,
            ...$allCrops,
            ...$allSeeds,

            //  'Certified seed produce' => $this->getCertifiedSeed(),
            //  'Value added RTC products' => $this->getValueAddedProducts()
        ];
    }
}
