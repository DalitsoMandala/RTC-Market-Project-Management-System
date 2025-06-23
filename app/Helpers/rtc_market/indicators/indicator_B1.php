<?php

namespace App\Helpers\rtc_market\indicators;

use Carbon\Carbon;

use App\Models\Project;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\FinancialYear;
use App\Models\IndicatorClass;
use App\Models\IndicatorTarget;
use App\Traits\FilterableQuery;
use App\Models\SubmissionPeriod;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Facades\DB;
use App\Helpers\ExchangeRateHelper;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
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



    protected $projectName = 'RTC MARKET';

    protected $lop = 30;
    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }
    public function Farmerbuilder(): Builder
    {



        return $this->applyFilters(RtcProductionFarmer::query()
            ->where('rtc_production_farmers.status', 'approved'));
    }



    public function Processorbuilder(): Builder
    {


        return $this->applyFilters(RtcProductionProcessor::query()
            ->where('rtc_production_processors.status', 'approved'));
    }





    public function findCropCount()
    {
        // If enterprise is set in constructor, return only that enterprise's total
        if ($this->enterprise) {
            $farmerTotal = $this->Farmerbuilder()->sum('prod_value_previous_season_usd_value');
            $processorTotal = $this->Processorbuilder()->sum('prod_value_previous_season_usd_value');

            return [
                strtolower(str_replace(' ', '_', $this->enterprise)) => $farmerTotal + $processorTotal,
            ];
        }

        // Otherwise, return totals for all enterprises
        $enterprises = ['Cassava', 'Potato', 'Sweet potato'];
        $result = [];

        foreach ($enterprises as $enterprise) {
            $farmerTotal = $this->Farmerbuilder()->where('enterprise', $enterprise)
                ->sum('prod_value_previous_season_usd_value');

            $processorTotal = $this->Processorbuilder()->where('enterprise', $enterprise)
                ->sum('prod_value_previous_season_usd_value');

            $result[strtolower(str_replace(' ', '_', $enterprise))] = $farmerTotal + $processorTotal;
        }

        return $result;
    }




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
            ...$allCrops,
            'Traders' => 0,
            'Farmers' => 0,
            'Processors' => 0,
            'Aggregators' => 0,
            'Transporters' => 0,
        ];
    }
}
