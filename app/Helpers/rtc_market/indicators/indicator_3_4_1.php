<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\RtcConsumption;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_4_1 extends base
{
    public function builder(): Builder
    {
        return $this->applyFilters(
            RtcConsumption::query()->where('status', 'approved')
        );
    }

    public function reportBuilder(): Builder
    {
        $indicatorId = Indicator::where('indicator_no', '3.4.1')->first()?->id;

        return $this->applyFilters(
            SubmissionReport::query()->where('indicator_id', $indicatorId),
            true
        );
    }

    public function consumptionData(): array
    {
        $data = $this->builder()
            ->where('entity_type', 'Household')
            ->selectRaw('
          COUNT(*) as total
        ')
            ->first();

        return [
            'Total' => (float) ($data->total ?? 0),

        ];
    }

    public function reportData(): array
    {
        // Fixed: Pass reportBuilder() instead of builder()
        return $this->getTotalReport($this->reportBuilder(), self::pullTotals('3.4.1'))->toArray();
    }

    public function getMergedData(): array
    {
        $marketData = $this->consumptionData();
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
        $disaggregations = $this->getIndicatorDisaggregations('3.4.1');

        // Map merged values into disaggregations key-by-key
        foreach ($this->getMergedData() as $key => $val) {
            $disaggregations->put($key, $val);
        }

        return $disaggregations->toArray();
    }
}
