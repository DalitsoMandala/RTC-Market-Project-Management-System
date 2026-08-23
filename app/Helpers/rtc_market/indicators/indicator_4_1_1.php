<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\Recruitment;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_4_1_1 extends base
{
    public function builder(): Builder
    {
        return $this->applyFilters(
            Recruitment::query()->where('status', 'approved')
        );
    }

    public function reportBuilder(): Builder
    {
        $indicatorId = Indicator::where('indicator_no', '4.1.1')->first()?->id;

        return $this->applyFilters(
            SubmissionReport::query()->where('indicator_id', $indicatorId),
            true
        );
    }

    public function recruitmentData(): array
    {
        $data = $this->builder()
            ->whereIn('group', ['Producer organization (PO)', 'Small Medium Enterprise (SME)'])
            ->where('type', 'Processors')
            ->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN enterprise = 'Cassava' THEN 1 END) as cassava,
            COUNT(CASE WHEN enterprise = 'Potato' THEN 1 END) as potato,
            COUNT(CASE WHEN enterprise = 'Sweet potato' THEN 1 END) as sweet_potato,
            COUNT(CASE WHEN `group` = 'Producer organization (PO)' THEN 1 END) as pos,
            COUNT(CASE WHEN `group` = 'Small Medium Enterprise (SME)' THEN 1 END) as smes
        ")
            ->first();

        return [
            'Total'        => (int) ($data->total ?? 0),
            'Cassava'      => (int) ($data->cassava ?? 0),
            'Potato'       => (int) ($data->potato ?? 0),
            'Sweet potato' => (int) ($data->sweet_potato ?? 0),
            'PO\'s'        => (int) ($data->pos ?? 0),
            'SMEs'         => (int) ($data->smes ?? 0),
        ];
    }

    public function reportData(): array
    {
        // Fixed: Pass reportBuilder() instead of builder()
        return $this->getTotalReport($this->reportBuilder(), self::pullTotals('4.1.1'))->toArray();
    }

    public function getMergedData(): array
    {
        $marketData = $this->recruitmentData();
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
        $disaggregations = $this->getIndicatorDisaggregations('4.1.1');

        // Map merged values into disaggregations key-by-key
        foreach ($this->getMergedData() as $key => $val) {
            $disaggregations->put($key, $val);
        }

        return $disaggregations->toArray();
    }
}
