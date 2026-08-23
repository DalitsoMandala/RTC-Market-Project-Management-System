<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\ProductionMarketingLog;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_2_1_2 extends base
{
    public function builder(): Builder
    {
        return $this->applyFilters(
            ProductionMarketingLog::query()->where('status', 'approved')
        );
    }

    public function reportBuilder(): Builder
    {
        $indicatorId = Indicator::where('indicator_no', '2.1.2')->first()?->id;

        return $this->applyFilters(
            SubmissionReport::query()->where('indicator_id', $indicatorId),
            true
        );
    }

    public function marketData(): array
    {
        // Conversion factor: 1 Acre = 0.404686 Hectares
        $builder = $this->builder()->selectRaw('
            COALESCE(SUM(area_grown_acre * 0.404686), 0) as hectares_grown_total,
            COALESCE(SUM(area_grown_acre * 0.404686 * CASE WHEN enterprise = "Cassava" THEN 1 ELSE 0 END), 0) as hectares_grown_cassava,
            COALESCE(SUM(area_grown_acre * 0.404686 * CASE WHEN enterprise = "Potato" THEN 1 ELSE 0 END), 0) as hectares_grown_potato,
            COALESCE(SUM(area_grown_acre * 0.404686 * CASE WHEN enterprise = "Sweet potato" THEN 1 ELSE 0 END), 0) as hectares_grown_sweet_potato,
            COALESCE(SUM(area_grown_acre * 0.404686 * CASE WHEN type_of_farming = "Table Potato" THEN 1 ELSE 0 END), 0) as basic_seed,
            COALESCE(SUM(area_grown_acre * 0.404686 * CASE WHEN type_of_farming = "Seed" THEN 1 ELSE 0 END), 0) as certified_seed
        ')->first();

        return [
            'Total'        => (float) $builder->hectares_grown_total,
            'Cassava'      => (float) $builder->hectares_grown_cassava,
            'Potato'       => (float) $builder->hectares_grown_potato,
            'Sweet potato' => (float) $builder->hectares_grown_sweet_potato,
            'Basic'        => (float) $builder->basic_seed,
            'Certified'    => (float) $builder->certified_seed,
        ];
    }

    public function reportData(): array
    {
        // Fixed: Pass reportBuilder() instead of builder()
        return $this->getTotalReport($this->reportBuilder(), self::pullTotals('2.1.2'))->toArray();
    }

    public function getMergedData(): array
    {
        $marketData = $this->marketData();
        $reportData = $this->reportData();

        $keys = array_unique(array_merge(array_keys($marketData), array_keys($reportData)));

        $merged = [];
        foreach ($keys as $key) {
            $merged[$key] = round(($marketData[$key] ?? 0) + ($reportData[$key] ?? 0), 2);
        }

        return $merged;
    }

    public function getDisaggregations(): array
    {
        $disaggregations = $this->getIndicatorDisaggregations('2.1.2');

        // Map merged values into disaggregations key-by-key
        foreach ($this->getMergedData() as $key => $val) {
            $disaggregations->put($key, $val);
        }

        return $disaggregations->toArray();
    }
}
