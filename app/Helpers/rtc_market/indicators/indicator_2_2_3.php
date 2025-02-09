<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_3
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


        $query = RtcProductionFarmer::query()->where('status', 'approved')
            ->with([
                'basicSeed',
                'certifiedSeed'
            ])->where('is_registered_seed_producer', true);




        if ($crop) {

            $query->where('enterprise', $crop);
            return $query;
        }

        return $this->applyFilters($query);
    }


    public function getCategoryPos($crop = null)
    {
        // Use builderFarmer with specified crop and filter by type
        return $this->builderFarmer($crop)

            ->where('type', 'Producer organization (PO)')
            ->count();
    }

    public function getCategoryIndividualFarmers($crop = null)
    {
        // Use builderFarmer with specified crop and filter by type
        return $this->builderFarmer($crop)

            ->where('type', 'Large scale farm')
            ->count();
    }

    public function getBasicSeed($crop = null)
    {
        $totalArea = 0;

        // Use the builderFarmer query with specified crop and filter by group
        $query = $this->builderFarmer($crop)->where('group', 'Early generation seed producer');

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
        $query = $this->builderFarmer($crop)->where('group', 'Seed multiplier');

        $query->chunk(100, function ($farmers) use (&$totalArea) {
            // Calculate the area for certified seeds
            $certifiedSeedArea = $farmers->pluck('certifiedSeed')->flatten()->sum('area');
            $totalArea += $certifiedSeedArea;
        });

        return $totalArea;
    }

    public function getCrop()
    {
        // Calculate counts for each crop with 'Seed multiplier' group
        $cassavaCount = $this->builderFarmer('Cassava')->count();
        $potatoCount = $this->builderFarmer('Potato')->count();
        $sweetPotatoCount = $this->builderFarmer('Sweet potato')->count();

        return [
            'cassava' => $cassavaCount,
            'potato' => $potatoCount,
            'sweet_potato' => $sweetPotatoCount,
        ];
    }
    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage seed multipliers with formal registration')->where('indicator_no', '2.2.3')->first();
        if (!$indicator) {
            Log::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }

    public function getDisaggregations()
    {
        $cropData = $this->getCrop(); // Store crop data to avoid redundant calls


        return [
            'Total (% Percentage)' => 0,
            'Cassava' => $cropData['cassava'],
            'Potato' => $cropData['potato'],
            'Sweet potato' => $cropData['sweet_potato'],
            'Basic' => $this->getBasicSeed(),
            'Certified' => $this->getCertifiedSeed(),
            'POs' => $this->getCategoryPos(),
            'Individual farmers not in POs' => $this->getCategoryIndividualFarmers(),
        ];
    }
}