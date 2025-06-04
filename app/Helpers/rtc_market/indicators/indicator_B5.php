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

        $query = RtcProductionProcessor::query()->with('followups')
            ->where('rtc_production_processors.status', 'approved');

        return $this->applyFilters($query);
    }

    public function FarmerFollowupbuilder(): Builder
    {



        $query = RpmFarmerFollowUp::query();



        return $this->applyFilters($query);
    }

    public function getTotal()
    {
        $crop = $this->findCropCount();
        $subTotal = $crop['cassava'] + $crop['sweet_potato'] + $crop['potato'];
        $indicator = $this->findIndicator();
        $baseline = $indicator->baseline->baseline_value ?? 0;
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);
        return $percentageIncrease->percentage();
    }
    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage Increase in the volume of RTC produced')->where('indicator_no', 'B5')->first();
        if (!$indicator) {
            Log::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }


    public function findCropCount()
    {
        // If enterprise is set in constructor, return only that enterprise's total
        if ($this->enterprise) {
            $farmerTotal = $this->builderFarmer()->sum('prod_value_previous_season_usd_value');
            $processorTotal = $this->Processorbuilder()->sum('prod_value_previous_season_usd_value');

            return [
                strtolower(str_replace(' ', '_', $this->enterprise)) => $farmerTotal + $processorTotal,
            ];
        }

        // Otherwise, return totals for all enterprises
        $enterprises = ['Cassava', 'Potato', 'Sweet potato'];
        $result = [];

        foreach ($enterprises as $enterprise) {
            $farmerTotal = $this->builderFarmer()->where('enterprise', $enterprise)
                ->sum('total_vol_production_previous_season');

            $processorTotal = $this->Processorbuilder()->where('enterprise', $enterprise)
                ->sum('total_vol_production_previous_season');

            $result[strtolower(str_replace(' ', '_', $enterprise))] = $farmerTotal + $processorTotal;
        }

        return $result;
    }





    public function followUpBuilder()
    {


        $query = $this->builderFarmer()->with('followups')->whereHas('followups');

        return $this->applyFilters($query);
    }



    public function getDisaggregations()
    {
        $crop = $this->findCropCount();

        // Define all possible crops with default 0 values
        $allCrops = [
            'Cassava' => 0,
            'Sweet potato' => 0,
            'Potato' => 0,
        ];

        // Merge actual values (if they exist)
        foreach ($allCrops as $key => $value) {
            $snakeKey = strtolower(str_replace(' ', '_', $key));
            if (isset($crop[$snakeKey])) {
                $allCrops[$key] = round($crop[$snakeKey], 2);
            }
        }
        return [
            'Total (% Percentage)' => 0,
            ...$allCrops
            //  'Certified seed produce' => $this->getCertifiedSeed(),
            //  'Value added RTC products' => $this->getValueAddedProducts()
        ];
    }
}