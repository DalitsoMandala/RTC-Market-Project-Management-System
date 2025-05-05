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
        // Calculate totals for each crop type from the main `rtc_production_farmers` table
        $cassavaTotalFarmer = $this->builderFarmer()->where('enterprise', '=', 'Cassava')->sum('total_vol_production_previous_season');
        $potatoTotalFarmer = $this->builderFarmer()->where('enterprise', '=', 'Potato')->sum('total_vol_production_previous_season');
        $sweetPotatoTotalFarmer = $this->builderFarmer()->where('enterprise', '=', 'Sweet potato')->sum('total_vol_production_previous_season');

        // Processor totals (following a similar approach for processors)
        $cassavaTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Cassava')->sum('total_vol_production_previous_season');
        $potatoTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Potato')->sum('total_vol_production_previous_season');
        $sweetPotatoTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Sweet potato')->sum('total_vol_production_previous_season');


        // Optionally, return the combined totals
        return [
            'cassava' => $cassavaTotalFarmer + $cassavaTotalProcessor,
            'potato' => $potatoTotalFarmer  + $potatoTotalProcessor,
            'sweet_potato' => $sweetPotatoTotalFarmer + $sweetPotatoTotalProcessor,
        ];
    }


    public function followUpBuilder()
    {


        $query = $this->builderFarmer()->with('followups')->whereHas('followups');

        return $this->applyFilters($query);
    }



    public function getDisaggregations()
    {

        return [
            'Total (% Percentage)' => 0,
            'Cassava' => $this->findCropCount()['cassava'],
            'Potato' => $this->findCropCount()['potato'],
            'Sweet potato' => $this->findCropCount()['sweet_potato'],
            //  'Certified seed produce' => $this->getCertifiedSeed(),
            //  'Value added RTC products' => $this->getValueAddedProducts()
        ];
    }
}