<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use Illuminate\Support\Facades\Log;
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
    use FilterableQuery;
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



        return $this->applyFilters(RtcProductionFarmer::query()->with('followups')
            ->where('rtc_production_farmers.status', 'approved'));
    }


    public function FarmerFollowupbuilder(): Builder
    {


        return $this->applyFilters(RpmFarmerFollowUp::query());
    }

    public function Processorbuilder(): Builder
    {


        return $this->applyFilters(RtcProductionProcessor::query()->with('followups')
            ->where('rtc_production_processors.status', 'approved'));
    }

    public function ProcessorFollowupbuilder(): Builder
    {

        return $this->applyFilters(RpmProcessorFollowUp::query());
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
