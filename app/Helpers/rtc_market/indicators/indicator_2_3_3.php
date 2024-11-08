<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_3_3
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
    // public function builderFarmer($crop = null): Builder
    // {


    //     $query = RtcProductionFarmer::query()->where('status', 'approved')->where('uses_market_information_systems', true);

    //     // Check if both reporting period and financial year are set
    //     if ($this->reporting_period || $this->financial_year) {
    //         // Apply filter for reporting period if it's set
    //         if ($this->reporting_period) {
    //             $query->where('period_month_id', $this->reporting_period);
    //         }

    //         // Apply filter for financial year if it's set
    //         if ($this->financial_year) {
    //             $query->where('financial_year_id', $this->financial_year);
    //         }

    //         // If no data is found, return an empty result
    //         if (!$query->exists()) {
    //             $query->whereIn('id', []); // Empty result filter
    //         }
    //     }

    //     // Filter by organization if set
    //     if ($this->organisation_id) {
    //         $query->where('organisation_id', $this->organisation_id);
    //     }


    //     if ($crop) {

    //         $query->where('enterprise', $crop);
    //         return $query;
    //     }

    //     return $query;
    // }


    // public function getBasicSeed($cropQuery = null)
    // {
    //     $totalArea = 0;

    //     // Use the provided crop query or the default builderFarmer query
    //     $query = $cropQuery ?? $this->builderFarmer()->where('group', 'Early generation seed producer');

    //     // Process the query in chunks
    //     $query->chunk(100, function ($farmers) use (&$totalArea) {
    //         // Pluck, flatten, and sum the area from the basicSeed relationship
    //         $basicSeedArea = $farmers->pluck('basicSeed')->flatten()->sum('area');
    //         $totalArea += $basicSeedArea;
    //     });

    //     return $totalArea;
    // }




    // public function getCertifiedSeed($cropQuery = null)
    // {
    //     $totalArea = 0;

    //     // Use the provided crop query or the default builderFarmer query
    //     $query = $cropQuery ?? $this->builderFarmer()->where('group', 'Seed multiplier');

    //     $query->chunk(100, function ($farmers) use (&$totalArea) {
    //         $certifiedSeedArea = $farmers->pluck('certifiedSeed')->flatten()->sum('area');
    //         $totalArea += $certifiedSeedArea;
    //     });

    //     return $totalArea;
    // }


    // public function getCrop()
    // {
    //     $queryCassava = $this->builderFarmer('Cassava')->where('is_registered_seed_producer', true)->where('group', 'Seed multiplier');
    //     $queryPotato = $this->builderFarmer('Potato')->where('is_registered_seed_producer', true)->where('group', 'Seed multiplier');
    //     $querySwPotato = $this->builderFarmer('Sweet potato')->where('is_registered_seed_producer', true)->where('group', 'Seed multiplier');




    //     return [

    //         'cassava' => $queryCassava->count(),
    //         'potato' => $queryPotato->count(),
    //         'sweet_potato' => $querySwPotato->count(),
    //     ];
    // }


    // public function findIndicator()
    // {
    //     $indicator = Indicator::where('indicator_name', 'Number of registered seed producers accessing markets through online Market Information System (MIS)')->where('indicator_no', '2.3.3')->first();
    //     if (!$indicator) {
    //         Log::error('Indicator not found');
    //         return null; // Or throw an exception if needed
    //     }

    //     return $indicator;
    // }

    // public function getActorType()
    // {
    //     $builder = $this->builderFarmer()->where('is_registered_seed_producer', true)->where('group', 'Seed multiplier');
    //     $pos = $builder->where('type', 'Producer organization (PO)')->count();
    //     $individual = $builder->where('type', 'Large scale farm')->count();
    //     return [
    //         'pos' => $pos,
    //         'individual' => $individual,
    //     ];
    // }
    // public function getMarketType()
    // {
    //     // Build the query for registered seed producers who are seed multipliers
    //     $builder = $this->builderFarmer()->where('is_registered_seed_producer', true)
    //         ->where('group', 'Seed multiplier');

    //     // Initialize variables to store the total count of doms and intermarkets
    //     $totalDoms = 0;
    //     $totalInterMarkets = 0;

    //     // Chunk the results to process them in smaller batches
    //     $builder->chunk(100, function ($farmers) use (&$totalDoms, &$totalInterMarkets) {
    //         foreach ($farmers as $farmer) {
    //             // Sum up the number of doms for each farmer
    //             $totalDoms += $farmer->doms()->count();

    //             // Sum up the number of intermarkets for each farmer
    //             $totalInterMarkets += $farmer->intermarkets()->count();
    //         }
    //     });

    //     // Debug to see the total counts of doms and intermarkets
    //     return [
    //         'total_doms' => $totalDoms,
    //         'total_intermarkets' => $totalInterMarkets
    //     ];

    //     // Optionally return the totals if needed
    //     // return [
    //     //     'total_doms_count' => $totalDoms,
    //     //     'total_intermarkets_count' => $totalInterMarkets,
    //     // ];
    // }

    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Number of registered seed producers accessing markets through online Market Information System (MIS)')->where('indicator_no', '2.3.3')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

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
        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }




        return $query;
    }

    public function getTotals()
    {

        $builder = $this->builder()->get();

        $indicator = Indicator::where('indicator_name', 'Number of registered seed producers accessing markets through online Market Information System (MIS)')->where('indicator_no', '2.3.3')->first();
        $disaggregations = $indicator->disaggregations;
        $data = collect([]);
        $disaggregations->pluck('name')->map(function ($item) use (&$data) {
            $data->put($item, 0);
        });




        $this->builder()->chunk(100, function ($models) use (&$data) {
            $models->each(function ($model) use (&$data) {
                // Decode the JSON data from the model
                $json = collect(json_decode($model->data, true));

                // Add the values for each key to the totals
                foreach ($data as $key => $dt) {
                    if ($json->has($key)) {
                        $data->put($key, $data->get($key) + $json[$key]);
                    }
                }
            });
        });

        return $data;
    }

    public function getDisaggregations()
    {

        $total = $this->getTotals()['Domestic markets'] + $this->getTotals()['International markets'];
        $cassava = $this->getTotals()['Cassava'];
        $potato = $this->getTotals()['Potato'];
        $sweet_potato = $this->getTotals()['Sweet potato'];
        $individual_farmers = $this->getTotals()['Individual farmers not in POs'];
        $pos = $this->getTotals()['POs'];
        return [
            'Total' => $total,
            'Cassava' => $cassava,
            'Potato' => $potato,
            'Sweet potato' => $sweet_potato,
            'Domestic markets' => $this->getTotals()['Domestic markets'],
            'International markets' => $this->getTotals()['International markets'],
            'Individual farmers not in POs' => $individual_farmers,
            'POs' => $pos,
            'Large scale commercial farmers' => $this->getTotals()['Large scale commercial farmers'],


        ];
    }
}
