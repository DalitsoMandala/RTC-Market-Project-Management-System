<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;

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
        $this->financial_year   = $financial_year;
        $this->organisation_id  = $organisation_id;
        $this->enterprise       = $enterprise;
    }

    public function getPercentage()
    {

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
        $enterprises = ['Cassava', 'Potato', 'Sweet potato'];
        $result      = [];

        foreach ($enterprises as $enterprise) {
            // We sum everything for this crop across both tables
            $farmerTotal    = $this->Farmerbuilder()->where('enterprise', $enterprise)->sum('prod_value_previous_season_usd_value');
            $processorTotal = $this->Processorbuilder()->where('enterprise', $enterprise)->sum('prod_value_previous_season_usd_value');

            $result[strtolower(str_replace(' ', '_', $enterprise))] = $farmerTotal + $processorTotal;
        }

        return $result;
    }

    public function findActorTotals()
    {
        $actors = ['Traders', 'Farmers', 'Processors', 'Aggregators', 'Transporters'];
        $result = [];

        foreach ($actors as $type) {
            // IMPORTANT: We must filter by BOTH the actor type AND the allowed enterprises
            // to ensure we are looking at the same dataset as findCropCount.
            $allowedCrops = ['Cassava', 'Potato', 'Sweet potato'];

            $farmerTotal = $this->Farmerbuilder()
                ->where('type', $type)
                ->whereIn('enterprise', $allowedCrops)
                ->sum('prod_value_previous_season_usd_value');

            $processorTotal = $this->Processorbuilder()
                ->where('type', $type)
                ->whereIn('enterprise', $allowedCrops)
                ->sum('prod_value_previous_season_usd_value');

            $result[$type] = $farmerTotal + $processorTotal;
        }

        return $result;
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
            'Total'            => 0,
            'Male'             => 0,
            'Female'           => 0,
            'Farming'          => 0,
            'Processing'       => 0,
            'Aggregation'      => 0,
            'Transportation'   => 0,
            'Trading'          => 0,
            'Employees'        => 0,
            'Cassava'          => 0,
            'Potato'           => 0,
            'Sweet potato'     => 0,
            'Youth (18-35yrs)' => 0,
        ];
    }
}
