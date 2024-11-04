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


        $farmer = $this->builderFarmer()
            ->leftJoin('rpm_farmer_follow_ups', 'rpm_farmer_follow_ups.rpm_farmer_id', '=', 'rtc_production_farmers.id') // Assuming the related table has `farmer_id` to reference the main table
            ->select([
                DB::raw('COUNT(rtc_production_farmers.prod_value_previous_season_usd_value) AS Total'),

                // Sum from main table (farmers) for Cassava
                DB::raw("SUM(CASE WHEN rtc_production_farmers.enterprise = 'Cassava' THEN rtc_production_farmers.prod_value_previous_season_usd_value ELSE 0 END) AS Cassava_total"),

                // Sum from related table (related_table) for Cassava
                DB::raw("SUM(CASE WHEN rtc_production_farmers.enterprise = 'Cassava' THEN rpm_farmer_follow_ups.prod_value_previous_season_usd_value ELSE 0 END) AS Related_Cassava_total"),

                // Sum from main table (farmers) for Sweet potato
                DB::raw("SUM(CASE WHEN rtc_production_farmers.enterprise = 'Sweet potato' THEN rtc_production_farmers.prod_value_previous_season_usd_value ELSE 0 END) AS Sweet_potato_total"),

                // Sum from related table (related_table) for Sweet potato
                DB::raw("SUM(CASE WHEN rtc_production_farmers.enterprise = 'Sweet potato' THEN rpm_farmer_follow_ups.prod_value_previous_season_usd_value ELSE 0 END) AS Related_Sweet_potato_total"),

                // Sum from main table (farmers) for Potato
                DB::raw("SUM(CASE WHEN rtc_production_farmers.enterprise = 'Potato' THEN rtc_production_farmers.prod_value_previous_season_usd_value ELSE 0 END) AS Potato_total"),

                // Sum from related table (related_table) for Potato
                DB::raw("SUM(CASE WHEN rtc_production_farmers.enterprise = 'Potato' THEN rpm_farmer_follow_ups.prod_value_previous_season_usd_value ELSE 0 END) AS Related_Potato_total"),
            ])
            ->where('rtc_production_farmers.status', '=', 'approved')

            ->first()
            ->toArray();







        // Optionally, print out the combined array
        return [
            'cassava' => $farmer['Cassava_total'] + $farmer['Related_Cassava_total'],
            'potato' => $farmer['Potato_total'] + $farmer['Related_Potato_total'],
            'sweet_potato' => $farmer['Sweet_potato_total'] + $farmer['Related_Sweet_potato_total'],
        ];
    }


    public function followUpBuilder()
    {


        return $this->builderFarmer()->with('followups')->whereHas('followups');
    }



    public function getDisaggregations()
    {

        return [
            'Total (% Percentage)s' => $this->getTotal(),
            'Cassava' => round($this->findCropCount()['cassava'], 2),
            'Potato' => round($this->findCropCount()['potato'], 2),
            'Sweet potato' => round($this->findCropCount()['sweet_potato'], 2),
            //  'Certified seed produce' => $this->getCertifiedSeed(),
            //  'Value added RTC products' => $this->getValueAddedProducts()
        ];
    }
}
