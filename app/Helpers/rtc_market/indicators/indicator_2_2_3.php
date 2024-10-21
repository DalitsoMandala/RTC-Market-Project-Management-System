<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_3
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
        $query = $cropQuery ?? $this->builderFarmer()->where('group', 'Early generation seed producer')
            ->where('is_registered_seed_producer', true);

        // Process the query in chunks
        $query->chunk(100, function ($farmers) use (&$totalArea) {
            // Pluck, flatten, and sum the area from the basicSeed relationship
            $basicSeedArea = $farmers->pluck('basicSeed')->flatten()->sum('area');
            $totalArea += $basicSeedArea;
        });

        return $totalArea;
    }


    public function getCategoryPos($cropQuery = null)
    {


        // Use the provided crop query or the default builderFarmer query
        $query = $cropQuery ?? $this->builderFarmer()
            ->where('is_registered_seed_producer', true)
            ->where('type', 'Producer organization (PO)');



        return $query->count();
    }

    public function getCategoryIndividualFarmers($cropQuery = null)
    {


        // Use the provided crop query or the default builderFarmer query
        $query = $cropQuery ?? $this->builderFarmer()
            ->where('is_registered_seed_producer', true)
            ->where('type', 'Large scale farm');

        return $query->count();
    }


    public function getCertifiedSeed($cropQuery = null)
    {
        $totalArea = 0;

        // Use the provided crop query or the default builderFarmer query
        $query = $cropQuery ?? $this->builderFarmer()->where('group', 'Seed multiplier')
            ->where('is_registered_seed_producer', true);

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
        $total = $this->builderFarmer()
            ->where('group', 'Seed multiplier')
            ->where('is_registered_seed_producer', true)->count();

        // Subtotal based on Cassava, Potato, and Sweet potato
        $subTotal = $total;

        // Retrieve the indicator to get the baseline
        $indicator = $this->findIndicator();

        // Get the baseline value, defaulting to 0 if the indicator or baseline doesn't exist
        $baseline = $indicator->baseline->baseline_value ?? 0;

        // Calculate the percentage increase based on the subtotal and baseline
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);

        $finalTotalPercentage = $percentageIncrease->percentage();


        return [
            'Total (% Percentage)' => $finalTotalPercentage,
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
