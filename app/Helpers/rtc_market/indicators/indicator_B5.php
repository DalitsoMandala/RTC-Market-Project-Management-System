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

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
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
                // No data found, return an empty collection
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

    public function getFarmerContractual()
    {
        return RpmFarmerConcAgreement::query();

    }

    public function getFarmerDom()
    {
        return RpmFarmerDomMarket::query();

    }

    public function getFarmerInter()
    {
        return RpmFarmerInterMarket::query();

    }

    public function getProcessorContractual()
    {
        return RpmProcessorConcAgreement::query();

    }

    public function getProcessorDom()
    {

        return RpmProcessorDomMarket::query();
    }

    public function getProcessorInter()
    {
        return RpmProcessorInterMarket::query();

    }


    public function getTotal()
    {
        $farmer = $this->builderFarmer()->get()->sum('total_vol_production_previous_season');
        $processor = $this->builderProcessor()->get()->sum('total_vol_production_previous_season');

        $farmerContractual = $this->getFarmerContractual()->get()->sum('volume_sold_previous_period');
        $farmerDom = $this->getFarmerDom()->get()->sum('volume_sold_previous_period');
        $farmerInter = $this->getFarmerInter()->get()->sum('volume_sold_previous_period');
        $processorContractual = $this->getProcessorContractual()->get()->sum('volume_sold_previous_period');
        $processorDom = $this->getProcessorDom()->get()->sum('volume_sold_previous_period');
        $processorInter = $this->getProcessorInter()->get()->sum('volume_sold_previous_period');
        $farmerDetail = $farmer + $farmerContractual + $farmerDom + $farmerInter;
        $processorDetail = $processor + $processorContractual + $processorDom + $processorInter;

        return $farmerDetail + $processorDetail;


    }

    public function getCropTotal()
    {

        $farmerDOm = $this->getFarmerDom()->select([
            DB::raw("SUM(CASE WHEN crop_type = 'POTATO' THEN 1 ELSE 0 END) as potato"),
            DB::raw("SUM(CASE WHEN crop_type = 'CASSAVA' THEN 1 ELSE 0 END) as cassava"),
            DB::raw("SUM(CASE WHEN crop_type = 'SWEET POTATO' THEN 1 ELSE 0 END) as sweet_potato"),
        ])->first();

        $farmerInter = $this->getFarmerInter()->select([
            DB::raw("SUM(CASE WHEN crop_type = 'POTATO' THEN 1 ELSE 0 END) as potato"),
            DB::raw("SUM(CASE WHEN crop_type = 'CASSAVA' THEN 1 ELSE 0 END) as cassava"),
            DB::raw("SUM(CASE WHEN crop_type = 'SWEET POTATO' THEN 1 ELSE 0 END) as sweet_potato"),
        ])->first();


        $processorDOm = $this->getProcessorDom()->select([
            DB::raw("SUM(CASE WHEN crop_type = 'POTATO' THEN 1 ELSE 0 END) as potato"),
            DB::raw("SUM(CASE WHEN crop_type = 'CASSAVA' THEN 1 ELSE 0 END) as cassava"),
            DB::raw("SUM(CASE WHEN crop_type = 'SWEET POTATO' THEN 1 ELSE 0 END) as sweet_potato"),
        ])->first();

        $processorInter = $this->getProcessorInter()->select([
            DB::raw("SUM(CASE WHEN crop_type = 'POTATO' THEN 1 ELSE 0 END) as potato"),
            DB::raw("SUM(CASE WHEN crop_type = 'CASSAVA' THEN 1 ELSE 0 END) as cassava"),
            DB::raw("SUM(CASE WHEN crop_type = 'SWEET POTATO' THEN 1 ELSE 0 END) as sweet_potato"),
        ])->first();

        $totalPotato = $farmerDOm->potato + $farmerInter->potato + $processorDOm->potato + $processorInter->potato;
        $totalCassava = $farmerDOm->cassava + $farmerInter->cassava + $processorDOm->cassava + $processorInter->cassava;
        $totalSweetPotato = $farmerDOm->sweet_potato + $farmerInter->sweet_potato + $processorDOm->sweet_potato + $processorInter->sweet_potato;

        return [
            'Potato' => $totalPotato,
            'Cassava' => $totalCassava,
            'Sweet potato' => $totalSweetPotato,
        ];
    }

    public function followUpBuilder()
    {

        $farmer = $this->builder()->pluck('id');

        return RpmFarmerFollowUp::query()->whereIn('rpm_farmer_id', $farmer);
    }

    public function followUpBuilderProcessor()
    {
        $processor = $this->builderProcessor()->pluck('id');
        return RpmProcessorFollowUp::query()->whereIn('rpm_processor_id', $processor);
    }

    public function getCertifiedSeed()
    {
        $farmer = $this->builderFarmer()->where('uses_certified_seed', 1)->get()->count();
        $farmerFollowup = $this->followUpBuilder()->where('uses_certified_seed', 1)->get()->count();

        return $farmer + $farmerFollowup;
    }

    public function getValueAddedProducts()
    {
        $farmerDOm = $this->getFarmerDom()->where('product_type', 'VALUE ADDED PRODUCTS')->get()->count();
        $farmerInter = $this->getFarmerInter()->where('product_type', 'VALUE ADDED PRODUCTS')->get()->count();
        $farmerConc = $this->getFarmerContractual()->where('product_type', 'VALUE ADDED PRODUCTS')->get()->count();
        $processorDOm = $this->getProcessorDom()->where('product_type', 'VALUE ADDED PRODUCTS')->get()->count();
        $processorInter = $this->getProcessorInter()->where('product_type', 'VALUE ADDED PRODUCTS')->get()->count();
        $processorConc = $this->getProcessorContractual()->where('product_type', 'VALUE ADDED PRODUCTS')->get()->count();


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