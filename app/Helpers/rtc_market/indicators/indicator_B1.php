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
            ->whereIn('type', [
                'Traders',
                'Farmers',
                'Processors',
                'Aggregators',
                'Transporters',
            ])
            ->where('rtc_production_farmers.status', 'approved'));
    }



    public function Processorbuilder(): Builder
    {


        return $this->applyFilters(RtcProductionProcessor::query()
          ->whereIn('type', [
                'Traders',
                'Farmers',
                'Processors',
                'Aggregators',
                'Transporters',
            ])
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






    public function findActorTotals()
    {
        $actors = [
            'Traders',
            'Farmers',
            'Processors',
            'Aggregators',
            'Transporters',
        ];

        $results = [];

        foreach ($actors as  $type) {

            $farmerBuilder = $this->Farmerbuilder()->where('type', $type);
            $processorBuilder = $this->Processorbuilder()->where('type', $type);

            // Apply enterprise filter if it exists
            if ($this->enterprise) {
                $farmerBuilder->where('enterprise', $this->enterprise);
                $processorBuilder->where('enterprise', $this->enterprise);
            }



            $results[$type] =
                $farmerBuilder->sum('prod_value_previous_season_usd_value') +
                $processorBuilder->sum('prod_value_previous_season_usd_value');
        }

        return $results;
    }

    public function getDisaggregations()
    {
        $crop = $this->findCropCount();

        // Define all possible crops with default 0 values
        $crops = [
            'Cassava'      => round($crop['cassava'] ?? 0, 2),
            'Sweet potato' => round($crop['sweet_potato'] ?? 0, 2),
            'Potato'       => round($crop['potato'] ?? 0, 2),
        ];
        $actors = $this->findActorTotals();

        return [
            'Total (% Percentage)' => 0,
            ...$crops,
            ...$actors,
        ];
    }
}
