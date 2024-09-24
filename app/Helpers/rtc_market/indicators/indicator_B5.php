<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
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
    public function builder(): Builder
    {
        $query = HouseholdRtcConsumption::query()->where('status', 'approved');

        // Check if both reporting period and financial year are set
        if ($this->reporting_period && $this->financial_year) {
            // Filter by period and year
            $data = $query->where('period_month_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year);

            // If no data is found, force an empty result but don't exit early
            if (!$data->exists()) {
                $query->whereIn('id', []); // Empty result filter
            } else {
                $query = $data; // If data exists, use the filtered query
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

    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query()->where('status', 'approved');

        // Check if both reporting period and financial year are set
        if ($this->reporting_period && $this->financial_year) {
            // Filter by period and year
            $data = $query->where('period_month_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year);

            // If no data is found, force an empty result but don't exit early
            if (!$data->exists()) {
                $query->whereIn('id', []); // Empty result filter
            } else {
                $query = $data; // If data exists, use the filtered query
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


    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()->where('status', 'approved');

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

    public function getFarmerContractual()
    {
        return $this->builderFarmer()->with('agreements')->whereHas('agreements');

    }

    public function getFarmerDom()
    {
        return $this->builderFarmer()->with('doms')->whereHas('doms');


    }

    public function getFarmerInter()
    {
        return $this->builderProcessor()->with('intermarkets')->whereHas('intermarkets');


    }

    public function getProcessorContractual()
    {
        return $this->builderProcessor()->with('agreements')->whereHas('agreements');


    }

    public function getProcessorDom()
    {

        return $this->builderProcessor()->with('doms')->whereHas('doms');
    }

    public function getProcessorInter()
    {
        return $this->builderProcessor()->with('intermarkets')->whereHas('intermarkets');

    }
    public function getTotal()
    {
        $farmer = $this->builderFarmer()->get()->sum('total_vol_production_previous_season');
        $processor = $this->builderProcessor()->get()->sum('total_vol_production_previous_season');

        $farmerContractual = $this->getFarmerContractual()->get()->pluck('agreements')->flatten()->sum('volume_sold_previous_period');
        $farmerDom = $this->getFarmerDom()->get()->pluck('doms')->flatten()->sum('volume_sold_previous_period');
        $farmerInter = $this->getFarmerInter()->get()->pluck('intermarkets')->flatten()->sum('volume_sold_previous_period');
        $processorContractual = $this->getProcessorContractual()->get()->pluck('agreements')->flatten()->sum('volume_sold_previous_period');
        $processorDom = $this->getProcessorDom()->get()->pluck('doms')->flatten()->sum('volume_sold_previous_period');
        $processorInter = $this->getProcessorInter()->get()->pluck('intermarkets')->flatten()->sum('volume_sold_previous_period');
        $farmerDetail = $farmer + $farmerContractual + $farmerDom + $farmerInter;
        $processorDetail = $processor + $processorContractual + $processorDom + $processorInter;

        return $farmerDetail + $processorDetail;


    }


    public function getCropTotal()
    {
        // Use getFarmerDom() to get the builder and then retrieve the flattened collection of doms
        $farmerDom = $this->getFarmerDom()->get()->pluck('doms')->flatten();

        // Aggregate crop types within the doms collection
        $totalPotato = $farmerDom->where('crop_type', 'POTATO')->count();
        $totalCassava = $farmerDom->where('crop_type', 'CASSAVA')->count();
        $totalSweetPotato = $farmerDom->where('crop_type', 'SWEET POTATO')->count();

        // Repeat similar logic for other related methods
        $farmerInter = $this->getFarmerInter()->get()->pluck('intermarkets')->flatten();
        $processorDom = $this->getProcessorDom()->get()->pluck('doms')->flatten();
        $processorInter = $this->getProcessorInter()->get()->pluck('intermarkets')->flatten();

        // Aggregate for other relationships
        $totalPotato += $farmerInter->where('crop_type', 'POTATO')->count();
        $totalCassava += $farmerInter->where('crop_type', 'CASSAVA')->count();
        $totalSweetPotato += $farmerInter->where('crop_type', 'SWEET POTATO')->count();

        $totalPotato += $processorDom->where('crop_type', 'POTATO')->count();
        $totalCassava += $processorDom->where('crop_type', 'CASSAVA')->count();
        $totalSweetPotato += $processorDom->where('crop_type', 'SWEET POTATO')->count();

        $totalPotato += $processorInter->where('crop_type', 'POTATO')->count();
        $totalCassava += $processorInter->where('crop_type', 'CASSAVA')->count();
        $totalSweetPotato += $processorInter->where('crop_type', 'SWEET POTATO')->count();

        return [
            'Potato' => $totalPotato,
            'Cassava' => $totalCassava,
            'Sweet potato' => $totalSweetPotato,
        ];
    }



    public function followUpBuilder()
    {


        return $this->builderFarmer()->with('followups')->whereHas('followups');


    }

    public function followUpBuilderProcessor()
    {

        return $this->builderProcessor()->with('followups')->whereHas('followups');

    }


    public function getCertifiedSeed()
    {

        $followups = $this->followUpBuilder()->get()->pluck('followups')->flatten();
        $farmer = $this->builderFarmer()->where('uses_certified_seed', 1)->get()->count();
        $farmerFollowup = $followups->where('uses_certified_seed', 1)->count();

        return $farmer + $farmerFollowup;
    }

    public function getValueAddedProducts()
    {
        $farmerDOm = $this->getFarmerDom()->get()->pluck('doms')->flatten()->where('product_type', 'VALUE ADDED PRODUCTS')->count();
        $farmerInter = $this->getFarmerInter()->get()->pluck('intermarkets')->flatten()->where('product_type', 'VALUE ADDED PRODUCTS')->count();
        $farmerConc = $this->getFarmerContractual()->get()->pluck('agreements')->flatten()->where('product_type', 'VALUE ADDED PRODUCTS')->count();
        $processorDOm = $this->getProcessorDom()->get()->pluck('doms')->flatten()->where('product_type', 'VALUE ADDED PRODUCTS')->count();
        $processorInter = $this->getProcessorInter()->get()->pluck('intermarkets')->flatten()->where('product_type', 'VALUE ADDED PRODUCTS')->count();
        $processorConc = $this->getProcessorContractual()->get()->pluck('agreements')->flatten()->where('product_type', 'VALUE ADDED PRODUCTS')->count();


        return $farmerDOm + $farmerInter + $farmerConc + $processorDOm + $processorInter + $processorConc;



    }

    public function getDisaggregations()
    {

        return [
            'Total' => $this->getTotal(),
            'Cassava' => $this->getCropTotal()['Cassava'],
            'Potato' => $this->getCropTotal()['Potato'],
            'Sweet potato' => $this->getCropTotal()['Sweet potato'],
            'Certified seed produce' => $this->getCertifiedSeed(),
            'Value added RTC products' => $this->getValueAddedProducts()
        ];

    }


}
