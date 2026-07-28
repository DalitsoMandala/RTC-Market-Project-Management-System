<?php
namespace App\Traits;

use App\Models\ReportStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait SharedReportsHelperTrait
{
    protected array $aggregateYears       = [];
    protected $current                    = 0;
    protected $totalIterations            = 0;
    protected int $errorCount             = 0;
    protected ?int $lastPersistedProgress = null;

    const IGNORED_YEARS = [1, 2, 3]; // Populate with your ignored year numbers which should be skipped during report generation and should match the values in the aggregateYears array. For example, if you want to ignore years 2020 and 2021, set this to [2020, 2021].
    const PROJECT_NAME  = 'RTC MARKET';

    protected function syncDisaggregations($report, array $disaggregations)
    {
        if (empty($disaggregations)) {
            // Option: If $disaggregations is empty, should it delete ALL existing data?
            // If so, uncomment the next line:
            // $report->data()->delete();
            return;
        }

        // 1. Delete items that are no longer present in the incoming payload
        // Assumes $disaggregations keys are the 'name' column (e.g., ['age' => 25, 'gender' => 'male'])
        $report->data()->whereNotIn('name', array_keys($disaggregations))->delete();

        // 2. Prepare the payload for a single bulk database roundtrip
        $now        = Carbon::now();
        $upsertData = [];

        foreach ($disaggregations as $name => $value) {
            $upsertData[] = [
                'system_report_id' => $report->id, // Replace 'report_id' with your actual foreign key column name
                'name'             => $name,
                'value'            => $value,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        // 3. Perform a single bulk UPSERT query
        // Parameters: (data_array, unique_identifying_columns, columns_to_update_on_duplicate)
        $report->data()->upsert(
            $upsertData,
            ['system_report_id', 'name'], // The composite unique index or primary key that prevents duplicates
            ['value', 'updated_at']       // Columns to update if the record already exists
        );
    }

// SharedReportsHelperTrait.php
    protected function updateProgress(bool $force = false, int $capMin = 0, int $capMax = 100): void
    {
        $this->current++;

        if ($this->totalIterations <= 0) {
            return;
        }

        // 1. Calculate and clamp local progress ratio between 0.0 and 1.0
        $localRatio = min(1.0, max(0.0, $this->current / $this->totalIterations));

        // 2. Map ratio to capMin/capMax range
        $calculatedProgress = $capMin + ($localRatio * ($capMax - $capMin));

        $isFinal  = $this->current >= $this->totalIterations;
        $progress = $isFinal ? $capMax : max($capMin, min($capMax, (int) round($calculatedProgress)));

        // 3. Dynamic throttling: Update roughly every 1% of total work (or at least every 1 item)
        $updateFrequency = max(1, (int) floor($this->totalIterations / 100));
        $shouldUpdate    = $force || $isFinal || ($this->current % $updateFrequency === 0);

        if ($shouldUpdate) {
            // 4. Use dynamic cache key and specify a cache TTL
            $reportId = $this->reportId ?? 1;
            Cache::put("report_progress_{$reportId}", $progress, now()->addHours(1));

            // 5. Update database directly in a single query (no extra SELECT query)
            ReportStatus::where('id', $reportId)->update([
                'status'   => ($isFinal && $capMax >= 100) ? 'completed' : 'processing',
                'progress' => $progress,
            ]);
        }
    }
}
