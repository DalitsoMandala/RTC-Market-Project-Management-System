<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\ProductionMarketingLog;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_3_8 extends base
{
    public function builder(): Builder
    {
        return $this->applyFilters(
            ProductionMarketingLog::query()->where('status', 'approved')
        );
    }

    public function reportBuilder(): Builder
    {
        $indicatorId = Indicator::where('indicator_no', '3.3.8')->first()?->id;

        return $this->applyFilters(
            SubmissionReport::query()->where('indicator_id', $indicatorId),
            true
        );
    }

    public function marketData(): array
    {
        $data = $this->builder()
            ->selectRaw('
            COALESCE(SUM(unit_weight_kg) / 1000, 0) as mtonnes_grown_total,

            COALESCE(
                SUM(
                    CASE
                        WHEN enterprise = "Cassava"
                        THEN unit_weight_kg
                        ELSE 0
                    END
                ) / 1000,
                0
            ) as mtonnes_grown_cassava,

            COALESCE(
                SUM(
                    CASE
                        WHEN enterprise = "Potato"
                        THEN unit_weight_kg
                        ELSE 0
                    END
                ) / 1000,
                0
            ) as mtonnes_grown_potato,

            COALESCE(
                SUM(
                    CASE
                        WHEN enterprise = "Sweet potato"
                        THEN unit_weight_kg
                        ELSE 0
                    END
                ) / 1000,
                0
            ) as mtonnes_grown_sweet_potato
        ')
            ->first();

        return [
            'Total'        => (float) ($data->mtonnes_grown_total ?? 0),
            'Cassava'      => (float) ($data->mtonnes_grown_cassava ?? 0),
            'Potato'       => (float) ($data->mtonnes_grown_potato ?? 0),
            'Sweet potato' => (float) ($data->mtonnes_grown_sweet_potato ?? 0),
        ];
    }

    public function reportData(): array
    {
        // Fixed: Pass reportBuilder() instead of builder()
        return $this->getTotalReport($this->reportBuilder(), self::pullTotals('3.3.8'))->toArray();
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
        $disaggregations = $this->getIndicatorDisaggregations('3.3.8');

        // Map merged values into disaggregations key-by-key
        foreach ($this->getMergedData() as $key => $val) {
            $disaggregations->put($key, $val);
        }

        return $disaggregations->toArray();
    }

}
