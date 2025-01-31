<?php

namespace App\Helpers\rtc_market\indicators;

use Log;
use Carbon\Carbon;
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
        // Farmers' totals for each crop
        $cassavaTotalFarmer = $this->Farmerbuilder()->where('enterprise', '=', 'Cassava')->sum('prod_value_previous_season_usd_value');
        $potatoTotalFarmer = $this->Farmerbuilder()->where('enterprise', '=', 'Potato')->sum('prod_value_previous_season_usd_value');
        $sweetPotatoTotalFarmer = $this->Farmerbuilder()->where('enterprise', '=', 'Sweet potato')->sum('prod_value_previous_season_usd_value');

        // Farmers' related follow-ups totals
        $cassavaFarmerFollowUps = $this->Farmerbuilder()
            ->where('enterprise', '=', 'Cassava')
            ->withSum('followups', 'prod_value_previous_season_usd_value')
            ->first();
        $cassavaRelatedTotalFarmer = $cassavaFarmerFollowUps ? $cassavaFarmerFollowUps->followups_sum_prod_value_previous_season_usd_value : 0;

        $potatoFarmerFollowUps = $this->Farmerbuilder()
            ->where('enterprise', '=', 'Potato')
            ->withSum('followups', 'prod_value_previous_season_usd_value')
            ->first();
        $potatoRelatedTotalFarmer = $potatoFarmerFollowUps ? $potatoFarmerFollowUps->followups_sum_prod_value_previous_season_usd_value : 0;

        $sweetPotatoFarmerFollowUps = $this->Farmerbuilder()
            ->where('enterprise', '=', 'Sweet potato')
            ->withSum('followups', 'prod_value_previous_season_usd_value')
            ->first();
        $sweetPotatoRelatedTotalFarmer = $sweetPotatoFarmerFollowUps ? $sweetPotatoFarmerFollowUps->followups_sum_prod_value_previous_season_usd_value : 0;

        // Processors' totals for each crop
        $cassavaTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Cassava')->sum('prod_value_previous_season_usd_value');
        $potatoTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Potato')->sum('prod_value_previous_season_usd_value');
        $sweetPotatoTotalProcessor = $this->Processorbuilder()->where('enterprise', '=', 'Sweet potato')->sum('prod_value_previous_season_usd_value');

        // Processors' related follow-ups totals
        $cassavaProcessorFollowUps = $this->Processorbuilder()
            ->where('enterprise', '=', 'Cassava')
            ->withSum('followups', 'prod_value_previous_season_usd_value')
            ->first();
        $cassavaRelatedTotalProcessor = $cassavaProcessorFollowUps ? $cassavaProcessorFollowUps->followups_sum_prod_value_previous_season_usd_value : 0;

        $potatoProcessorFollowUps = $this->Processorbuilder()
            ->where('enterprise', '=', 'Potato')
            ->withSum('followups', 'prod_value_previous_season_usd_value')
            ->first();
        $potatoRelatedTotalProcessor = $potatoProcessorFollowUps ? $potatoProcessorFollowUps->followups_sum_prod_value_previous_season_usd_value : 0;

        $sweetPotatoProcessorFollowUps = $this->Processorbuilder()
            ->where('enterprise', '=', 'Sweet potato')
            ->withSum('followups', 'prod_value_previous_season_usd_value')
            ->first();
        $sweetPotatoRelatedTotalProcessor = $sweetPotatoProcessorFollowUps ? $sweetPotatoProcessorFollowUps->followups_sum_prod_value_previous_season_usd_value : 0;

        // Combine totals for each crop
        return [
            'cassava' => $cassavaTotalFarmer + $cassavaRelatedTotalFarmer + $cassavaTotalProcessor + $cassavaRelatedTotalProcessor,
            'potato' => $potatoTotalFarmer + $potatoRelatedTotalFarmer + $potatoTotalProcessor + $potatoRelatedTotalProcessor,
            'sweet_potato' => $sweetPotatoTotalFarmer + $sweetPotatoRelatedTotalFarmer + $sweetPotatoTotalProcessor + $sweetPotatoRelatedTotalProcessor,
        ];
    }


    public function calculations() {}


    private function calculateCropValue($data, $crop, $year_id) {}

    private function calculateIndicatorData(&$countDataYear, $year_id, $total) {}

    public function findTotal()
    {
        // $previousValue = 0;
        // //  $currentDate = Carbon::today()->toDateString();
        // $currentDate = '2026-01-01 00:00:00';

        // // Query to find the record where the current date is on or after start_date and on or before end_date
        // $record = FinancialYear::query()
        //     ->whereDate('start_date', '<=', $currentDate)  // Current date is on or after start_date
        //     ->whereDate('end_date', '>=', $currentDate)    // Current date is on or before end_date
        //     ->where('project_id', 1)
        //     ->select('id', 'number', 'start_date', 'end_date', 'project_id')
        //     ->first();
        // $previousYear = $record->number - 1;
        // if ($previousYear == 1) {
        //     $indicator = $this->findIndicator();
        //     $baseline = $indicator->baseline->baseline_value ?? 0;
        //     $previousValue = $baseline;

        // } else {



        // }

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
            'Total (% Percentage)' => 0,
            'Cassava' => round($crop['cassava'], 2),
            'Sweet potato' => round($crop['sweet_potato'], 2),
            'Potato' => round($crop['potato'], 2),
        ];
    }
}
