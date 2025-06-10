<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Recruitment;
use App\Traits\FilterableQuery;

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
        $query = RtcProductionFarmer::query()->where('status', 'approved');

        return $this->applyFilters($query);
    }
    public function builder(): Builder
    {
        $query = Recruitment::query()->where('status', 'approved')

            ->where(function ($model) {

            $model->where('group', 'Producer organization (PO)')
                ->OrWhere('group', 'Large scale farm');
            })->where('sector', 'Private');





        return $this->applyFilters($query);
    }

    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()->where('status', 'approved')->where(function ($query) {

            $query->where('type', 'PRODUCER ORGANIZATION (PO)')->orWhere('type', 'LARGE SCALE FARM');
        })->where('sector', 'PRIVATE');;



        return $this->applyFilters($query);
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
        $builder = $this->builder();



        // Initialize all crops with 0 values
        $totals = [
            'Potato' => 0,
            'Cassava' => 0,
            'Sweet potato' => 0,

        ];


        // If enterprise is set, only count for that specific crop
        if ($this->enterprise) {
            $enterpriseKey = $this->enterprise;
            $totals[$enterpriseKey] = (clone $builder)
                ->where('enterprise', $enterpriseKey)
                ->count();
        }
        // Otherwise count for all crops
        else {
            $totals['Potato'] = (clone $builder)
                ->where('enterprise', 'Potato')
                ->count();

            $totals['Cassava'] = (clone $builder)
                ->where('enterprise', 'Cassava')
                ->count();

            $totals['Sweet potato'] = (clone $builder)
                ->where('enterprise', 'Sweet potato')
                ->count();
        }

        return $totals;
    }



    public function getMarketSegment()
    {
        $builder = $this->builderFarmer();

        if ($this->enterprise) {
        return [
                'Fresh' => [
                    $this->enterprise => (clone $builder)
                        ->where('enterprise', $this->enterprise)
                        ->where('market_segment_fresh', true)
                        ->count(),
                ],
                'Processed' => [
                    $this->enterprise => (clone $builder)
                        ->where('enterprise', $this->enterprise)
                        ->where('market_segment_processed', true)
                        ->count(),
                ],
        ];
    }

        return [
            'Fresh' => [
                'Cassava' => (clone $builder)
                    ->where('enterprise', 'Cassava')
                    ->where('market_segment_fresh', true)
                    ->count(),
                'Potato' => (clone $builder)
                    ->where('enterprise', 'Potato')
                    ->where('market_segment_fresh', true)
                    ->count(),
                'Sweet potato' => (clone $builder)
                    ->where('enterprise', 'Sweet potato')
                    ->where('market_segment_fresh', true)
                    ->count(),
            ],
            'Processed' => [
                'Cassava' => (clone $builder)
                    ->where('enterprise', 'Cassava')
                    ->where('market_segment_processed', true)
                    ->count(),
                'Potato' => (clone $builder)
                    ->where('enterprise', 'Potato')
                    ->where('market_segment_processed', true)
                    ->count(),
                'Sweet potato' => (clone $builder)
                    ->where('enterprise', 'Sweet potato')
                    ->where('market_segment_processed', true)
                    ->count(),
            ]
        ];
    }

    public function getDisaggregations()
    {
        $cropTotals = $this->getCropTotal();
        $marketSegments = $this->getMarketSegment();

        // Initialize the basic structure we want
        $result = [
            'Cassava' => $cropTotals['Cassava'],
            'Potato' => $cropTotals['Potato'],
            'Sweet potato' => $cropTotals['Sweet potato'],
            'Fresh' => 0,
            'Processed' => 0
        ];



        return $result;
    }
}