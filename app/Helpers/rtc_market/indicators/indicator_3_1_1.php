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
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmProcessorConcAgreement;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_1_1
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
        $query = RtcProductionFarmer::query()->where('status', 'approved')
            ->where('type', 'Producer organization (PO)')
            ->where('type', 'Large scale farm')
            ->where('sector', 'Private');



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


    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()->where('status', 'approved')->where(function ($query) {

            $query->where('type', 'PRODUCER ORGANIZATION (PO)')->orWhere('type', 'LARGE SCALE FARM');
        })->where('sector', 'PRIVATE');;

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



    public function getCropTotal()
    {
        // Build the base query for farmers with type 'Large scale farm' and sector 'Private'
        $builder = $this->builderFarmer();

        // Clone the query builder for each crop type to avoid overwriting the base query
        $totalPotato = (clone $builder)->where('enterprise', 'Potato')->count();
        $totalCassava = (clone $builder)->where('enterprise', 'Cassava')->count();
        $totalSweetPotato = (clone $builder)->where('enterprise', 'Sweet potato')->count();

        // Return the totals as an array
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
    public function getMarketSegment()
    {
        $builder = $this->builderFarmer();
        return [
            'Fresh' => $builder->where('market_segment_fresh', true)->count(),
            'Processed' => $builder->where('market_segment_processed', true)->count(),
        ];
    }


    public function getDisaggregations()
    {
        $builder = $this->builderFarmer();

        return [
            'Total' => $builder->count(),
            'Cassava' => $this->getCropTotal()['Cassava'],
            'Potato' => $this->getCropTotal()['Potato'],
            'Sweet potato' => $this->getCropTotal()['Sweet potato'],
            'Fresh' => $this->getMarketSegment()['Fresh'],
            'Processed' => $this->getMarketSegment()['Processed'],
        ];
    }
}
