<?php

namespace App\Helpers\rtc_market\indicators;

use Log;
use App\Models\Project;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\FinancialYear;
use App\Models\IndicatorClass;
use App\Models\IndicatorTarget;
use App\Models\SubmissionPeriod;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Facades\DB;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use App\Models\RpmProcessorFollowUp;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log as Logger;


class indicator_B1
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $target_year_id;

    protected $projectName = 'RTC MARKET';

    protected $lop = 30;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;
    }
    public function Farmerbuilder(): Builder
    {

        $query = RtcProductionFarmer::query()->with('followups')
            ->where('rtc_production_farmers.status', 'approved');



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

    public function ProcessorFollowupbuilder(): Builder
    {

        $query = RpmProcessorFollowUp::query();

        return $query;
    }


    public function findCropCount()
    {


        $farmer = $this->Farmerbuilder()
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
            //  ->where('rtc_production_farmers.status', '=', 'approved')

            ->first()
            ->toArray();



        $processor = $this->Processorbuilder()->leftJoin('rpm_processor_follow_ups', 'rpm_processor_follow_ups.rpm_processor_id', '=', 'rtc_production_processors.id') // Assuming the related table has `farmer_id` to reference the main table
            ->select([
                DB::raw('COUNT(rtc_production_processors.prod_value_previous_season_usd_value) AS Total'),

                // Sum from main table (farmers) for Cassava
                DB::raw("SUM(CASE WHEN rtc_production_processors.enterprise = 'Cassava' THEN rtc_production_processors.prod_value_previous_season_usd_value ELSE 0 END) AS Cassava_total"),

                // Sum from related table (related_table) for Cassava
                DB::raw("SUM(CASE WHEN rtc_production_processors.enterprise = 'Cassava' THEN rpm_processor_follow_ups.prod_value_previous_season_usd_value ELSE 0 END) AS Related_Cassava_total"),

                // Sum from main table (farmers) for Sweet potato
                DB::raw("SUM(CASE WHEN rtc_production_processors.enterprise = 'Sweet potato' THEN rtc_production_processors.prod_value_previous_season_usd_value ELSE 0 END) AS Sweet_potato_total"),

                // Sum from related table (related_table) for Sweet potato
                DB::raw("SUM(CASE WHEN rtc_production_processors.enterprise = 'Sweet potato' THEN rpm_processor_follow_ups.prod_value_previous_season_usd_value ELSE 0 END) AS Related_Sweet_potato_total"),

                // Sum from main table (farmers) for Potato
                DB::raw("SUM(CASE WHEN rtc_production_processors.enterprise = 'Potato' THEN rtc_production_processors.prod_value_previous_season_usd_value ELSE 0 END) AS Potato_total"),

                // Sum from related table (related_table) for Potato
                DB::raw("SUM(CASE WHEN rtc_production_processors.enterprise = 'Potato' THEN rpm_processor_follow_ups.prod_value_previous_season_usd_value ELSE 0 END) AS Related_Potato_total"),
            ])

            //   ->where('rtc_production_processors.status', '=', 'approved')
            ->first()
            ->toArray();


        $combined = [];

        foreach ($farmer as $key => $value) {
            // Check if the key exists in the second array
            if (array_key_exists($key, $processor)) {
                // If the value is numeric, sum the values from both arrays
                if (is_numeric($value)) {
                    $combined[$key] = $value + $processor[$key];
                } else {
                    // For non-numeric values (e.g., followups), just keep the value from the first array
                    $combined[$key] = $value;
                }
            } else {
                // If the key doesn't exist in the second array, just keep the value from the first array
                $combined[$key] = $value;
            }
        }

        // Optionally, print out the combined array
        return [
            'cassava' => $combined['Cassava_total'] + $combined['Related_Cassava_total'],
            'potato' => $combined['Potato_total'] + $combined['Related_Potato_total'],
            'sweet_potato' => $combined['Sweet_potato_total'] + $combined['Related_Sweet_potato_total'],
        ];
    }

    public function calculations()
    {
    }


    private function calculateCropValue($data, $crop, $year_id)
    {
    }

    private function calculateIndicatorData(&$countDataYear, $year_id, $total)
    {
    }

    public function findTotal()
    {

        // $farmer = $this->Farmerbuilder()->where('status', 'approved')->select('prod_value_previous_season_usd_value')->sum('prod_value_previous_season_usd_value');
        // $farmer_followup =  $this->FarmerFollowupbuilder()->where('status', 'approved')->select('prod_value_previous_season_usd_value')->sum('prod_value_previous_season_usd_value');

        // $processor = $this->Processorbuilder()->where('status', 'approved')->select('prod_value_previous_season_usd_value')->sum('prod_value_previous_season_usd_value');
        // $processor_followup =  $this->ProcessorFollowupbuilder()->where('status', 'approved')->select('prod_value_previous_season_usd_value')->sum('prod_value_previous_season_usd_value');

        // $subTotal = $farmer + $farmer_followup + $processor + $processor_followup;
        $crop = $this->findCropCount();
        $subTotal = $crop['cassava'] + $crop['sweet_potato'] + $crop['potato'];
        $indicator = $this->findIndicator();
        $baseline = $indicator->baseline->baseline_value ?? 0;
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);
        return $percentageIncrease->percentage();
    }
    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities')->where('indicator_no', 'B1')->first();
        if (!$indicator) {
            Logger::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }
    public function getDisaggregations()
    {
        $total = $this->findTotal();
        $crop = $this->findCropCount();

        return [
            'Total (% Percentage)' => round($total, 2),
            'Cassava' => round($crop['cassava'], 2),
            'Sweet potato' => round($crop['sweet_potato'], 2),
            'Potato' => round($crop['potato'], 2),
        ];
    }
}
