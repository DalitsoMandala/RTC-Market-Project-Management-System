<?php

namespace App\Helpers\rtc_market\indicators;

use Log;
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
    public function Processorbuilder(): Builder
    {

        $query = RtcProductionProcessor::query()->with('followups')
            ->where('rtc_production_processors.status', 'approved');

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

        return $query;
    }

    public function FarmerFollowupbuilder(): Builder
    {



        $query = RpmFarmerFollowUp::query();



        return $query;
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

        // Calculate totals from the related `rpm_farmer_follow_ups` table for each crop type
        $cassavaFarmerFollowUps = $this->builderFarmer()
            ->where('enterprise', '=', 'Cassava')
            ->withSum('followups', 'total_vol_production_previous_season')
            ->first();
        $cassavaRelatedTotalFarmer = $cassavaFarmerFollowUps ? $cassavaFarmerFollowUps->followups_sum_total_vol_production_previous_season : 0;

        $potatoFarmerFollowUps = $this->builderFarmer()
            ->where('enterprise', '=', 'Potato')
            ->withSum('followups', 'total_vol_production_previous_season')
            ->first();
        $potatoRelatedTotalFarmer = $potatoFarmerFollowUps ? $potatoFarmerFollowUps->followups_sum_total_vol_production_previous_season : 0;

        $sweetPotatoFarmerFollowUps = $this->builderFarmer()
            ->where('enterprise', '=', 'Sweet potato')
            ->withSum('followups', 'total_vol_production_previous_season')
            ->first();
        $sweetPotatoRelatedTotalFarmer = $sweetPotatoFarmerFollowUps ? $sweetPotatoFarmerFollowUps->followups_sum_total_vol_production_previous_season : 0;

        // Processor totals (following a similar approach for processors)
        $cassavaTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Cassava')->sum('total_vol_production_previous_season');
        $potatoTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Potato')->sum('total_vol_production_previous_season');
        $sweetPotatoTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Sweet potato')->sum('total_vol_production_previous_season');

        // Processor related follow-ups totals
        $cassavaProcessorFollowUps = $this->Processorbuilder()
            ->where('enterprise', '=', 'Cassava')
            ->withSum('followups', 'total_vol_production_previous_season')
            ->first();
        $cassavaRelatedTotalProcessor = $cassavaProcessorFollowUps ? $cassavaProcessorFollowUps->followups_sum_total_vol_production_previous_season : 0;

        $potatoProcessorFollowUps = $this->Processorbuilder()
            ->where('enterprise', '=', 'Potato')
            ->withSum('followups', 'total_vol_production_previous_season')
            ->first();
        $potatoRelatedTotalProcessor = $potatoProcessorFollowUps ? $potatoProcessorFollowUps->followups_sum_total_vol_production_previous_season : 0;

        $sweetPotatoProcessorFollowUps = $this->Processorbuilder()
            ->where('enterprise', '=', 'Sweet potato')
            ->withSum('followups', 'total_vol_production_previous_season')
            ->first();
        $sweetPotatoRelatedTotalProcessor = $sweetPotatoProcessorFollowUps ? $sweetPotatoProcessorFollowUps->followups_sum_total_vol_production_previous_season : 0;

        // Optionally, return the combined totals
        return [
            'cassava' => $cassavaTotalFarmer + $cassavaRelatedTotalFarmer + $cassavaTotalProcessor + $cassavaRelatedTotalProcessor,
            'potato' => $potatoTotalFarmer + $potatoRelatedTotalFarmer + $potatoTotalProcessor + $potatoRelatedTotalProcessor,
            'sweet_potato' => $sweetPotatoTotalFarmer + $sweetPotatoRelatedTotalFarmer + $sweetPotatoTotalProcessor + $sweetPotatoRelatedTotalProcessor,
        ];
    }


    public function followUpBuilder()
    {


        return $this->builderFarmer()->with('followups')->whereHas('followups');
    }



    public function getDisaggregations()
    {

        return [
            'Total (% Percentage)' => $this->getTotal(),
            'Cassava' => round($this->findCropCount()['cassava'], 2),
            'Potato' => round($this->findCropCount()['potato'], 2),
            'Sweet potato' => round($this->findCropCount()['sweet_potato'], 2),
            //  'Certified seed produce' => $this->getCertifiedSeed(),
            //  'Value added RTC products' => $this->getValueAddedProducts()
        ];
    }
}
