<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_1_1
{
    use FilterableQuery;

    protected $financial_year;
    protected $reporting_period;
    protected $organisation_id;
    protected $enterprise;

    protected $crops = [
        'Cassava',
        'Potato',
        'Sweet potato',
    ];

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year   = $financial_year;
        $this->organisation_id  = $organisation_id;
        $this->enterprise       = $enterprise;
    }

    /*
    |--------------------------------------------------------------------------
    | Builders
    |--------------------------------------------------------------------------
    */

    public function builder(): Builder
    {
        $query = RtcProductionFarmer::query()
            ->where('status', 'approved')
            ->whereIn('group', ['Producer organization (PO)', 'Large scale farm'])
            ->where('sector', 'Private')
        // Filter: Must be at least Fresh OR Processed
            ->where(function ($q) {
                $q->where('market_segment_fresh', true)
                    ->orWhere('market_segment_processed', true);
            });

        if ($this->enterprise) {
            $query->where('enterprise', $this->enterprise);
        }

        return $this->applyFilters($query);
    }

    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()
            ->where('status', 'approved')
            ->whereIn('group', ['Producer organization (PO)', 'Large scale farm'])
            ->where('sector', 'Private')
        // Same filter applied here
            ->where(function ($q) {
                $q->where('market_segment_fresh', true)
                    ->orWhere('market_segment_processed', true);
            });

        if ($this->enterprise) {
            $query->where('enterprise', $this->enterprise);
        }

        return $this->applyFilters($query);
    }

    /*
    |--------------------------------------------------------------------------
    | Agreements / Markets
    |--------------------------------------------------------------------------
    */

    public function getFarmerContractual()
    {
        return $this->builder()->whereHas('agreements');
    }

    public function getFarmerDom()
    {
        return $this->builder()->whereHas('doms');
    }

    public function getFarmerInter()
    {
        return $this->builder()->whereHas('intermarkets');
    }

    public function getProcessorContractual()
    {
        return $this->builderProcessor()->whereHas('agreements');
    }

    public function getProcessorDom()
    {
        return $this->builderProcessor()->whereHas('doms');
    }

    public function getProcessorInter()
    {
        return $this->builderProcessor()->whereHas('intermarkets');
    }

    /*
    |--------------------------------------------------------------------------
    | Crop Totals
    |--------------------------------------------------------------------------
    */

    private function cropCounts(Builder $builder)
    {
        $totals = array_fill_keys($this->crops, 0);

        if ($this->enterprise) {
            $totals[$this->enterprise] = (clone $builder)
                ->where('enterprise', $this->enterprise)

                ->count();
            return $totals;
        }

        foreach ($this->crops as $crop) {
            $totals[$crop] = (clone $builder)
                ->where('enterprise', $crop)
                ->count();
        }

        return $totals;
    }

    public function getCropTotals()
    {
        $farmer    = $this->cropCounts($this->builder());
        $processor = $this->cropCounts($this->builderProcessor());

        $totals = [];

        foreach ($this->crops as $crop) {
            $totals[$crop] = ($farmer[$crop] ?? 0) + ($processor[$crop] ?? 0);
        }

        return $totals;
    }

    /*
    |--------------------------------------------------------------------------
    | Market Segments
    |--------------------------------------------------------------------------
    */

    private function marketSegments(Builder $builder)
    {
        $result = [
            'Fresh'     => array_fill_keys($this->crops, 0),
            'Processed' => array_fill_keys($this->crops, 0),
        ];

        foreach ($this->crops as $crop) {

            if ($this->enterprise && $crop !== $this->enterprise) {
                continue;
            }

            $result['Fresh'][$crop] = (clone $builder)
                ->where('enterprise', $crop)
                ->where('market_segment_fresh', true)
                ->count();

            $result['Processed'][$crop] = (clone $builder)
                ->where('enterprise', $crop)
                ->where('market_segment_processed', true)
                ->count();
        }

        return $result;
    }

    public function getMarketSegmentTotal()
    {
        $farmer    = $this->marketSegments($this->builder());
        $processor = $this->marketSegments($this->builderProcessor());

        $total = [
            'Fresh'     => [],
            'Processed' => [],
        ];

        foreach ($this->crops as $crop) {
            $total['Fresh'][$crop] =
                ($farmer['Fresh'][$crop] ?? 0) +
                ($processor['Fresh'][$crop] ?? 0);

            $total['Processed'][$crop] =
                ($farmer['Processed'][$crop] ?? 0) +
                ($processor['Processed'][$crop] ?? 0);
        }

        return $total;
    }
    private function cropMarketCounts(Builder $builder)
    {
        $result = [];

        foreach ($this->crops as $crop) {
            $cropQuery = (clone $builder)->where('enterprise', $crop);

            $result[$crop] = [
                'Total'     => $cropQuery->count(),
                'Fresh'     => (clone $cropQuery)->where('market_segment_fresh', true)->count(),
                'Processed' => (clone $cropQuery)->where('market_segment_processed', true)->count(),

            ];
        }

        return $result;
    }
    /*
    |--------------------------------------------------------------------------
    | Final Disaggregation
    |--------------------------------------------------------------------------
    */

    public function getDisaggregations()
    {
        $farmerCounts    = $this->cropMarketCounts($this->builder());
        $processorCounts = $this->cropMarketCounts($this->builderProcessor());

        $crops = $this->crops;

        $totals = [
            'Total'        => 0,
            'Cassava'      => 0,
            'Potato'       => 0,
            'Sweet potato' => 0,
            'Fresh'        => 0,
            'Processed'    => 0,
        ];

        foreach ($crops as $crop) {
            $totalCrop     = ($farmerCounts[$crop]['Total'] ?? 0) + ($processorCounts[$crop]['Total'] ?? 0);
            $freshCrop     = ($farmerCounts[$crop]['Fresh'] ?? 0) + ($processorCounts[$crop]['Fresh'] ?? 0);
            $processedCrop = ($farmerCounts[$crop]['Processed'] ?? 0) + ($processorCounts[$crop]['Processed'] ?? 0);

            $totals[$crop]        = $totalCrop;
            $totals['Fresh']     += $freshCrop;
            $totals['Processed'] += $processedCrop;
            $totals['Total']     += $totalCrop;
        }
        return [
            'Total'        => 0,
            'Cassava'      => 0,
            'Potato'       => 0,
            'Sweet potato' => 0,
            'Fresh'        => 0,
            'Processed'    => 0,
        ];
    }
}
