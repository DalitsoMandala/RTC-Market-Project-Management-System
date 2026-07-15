<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait SharedReportsHelperTrait
{
    protected array $aggregateYears = [];
    protected $current              = 0;
    protected $totalIterations      = 0;
    protected int $errorCount       = 0;

    const IGNORED_YEARS = []; // Populate with your ignored year numbers
    const PROJECT_NAME  = 'RTC MARKET';

    protected function syncDisaggregations($report, array $disaggregations)
    {
        if (empty($disaggregations)) {
            return;
        }

        $existing = $report->data()->pluck('name', 'id')->toArray();
        $toDelete = array_diff($existing, array_keys($disaggregations));

        if (! empty($toDelete)) {
            $report->data()->whereIn('id', array_keys($toDelete))->delete();
        }

        $now        = Carbon::now();
        $upsertData = [];

        /** UPSERT ALL DISAGGREGATIONS */
        foreach ($disaggregations as $key => $value) {
            $report->data()->updateOrCreate(
                ['name' => $key],
                ['value' => $value]
            );
        }
    }

    protected function updateProgress()
    {
        $this->current++;
        if ($this->current % 100 === 0) {
            $progress = round(($this->current / $this->totalIterations) * 100);
            Cache::put('report_progress', $progress);
        }
    }
}
