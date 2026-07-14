<?php
namespace App\Traits;

use App\Helpers\CoreFunctions;
use App\Models\AggregatedReport;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorClass;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\SystemReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait ReportsTrait
{
    protected array $aggregateYears = [];
    const IGNORED_YEARS             = [];
    const PROJECT_NAME              = 'RTC MARKET';

    protected $current         = 0;
    protected $totalIterations = 0;
    protected int $errorCount  = 0;

    protected ?int $financial_year_id   = null;
    protected ?int $project_id          = null;
    protected ?int $reporting_period_id = null;
    protected ?int $organisation_id     = null;
    protected ?int $indicator_id        = null;
    // In ReportsTrait, replace __construct with:
    public function initReportsTrait(
        ?int $financial_year_id = null,
        ?int $project_id = null,
        ?int $reporting_period_id = null,
        ?int $organisation_id = null,
        ?int $indicator_id = null,
    ): void {
        $this->financial_year_id   = $financial_year_id;
        $this->project_id          = $project_id;
        $this->reporting_period_id = $reporting_period_id;
        $this->organisation_id     = $organisation_id;
        $this->indicator_id        = $indicator_id;

        $this->aggregateYears = FinancialYear::whereHas('project', fn($q) => $q->where('name', self::PROJECT_NAME))
            ->get()
            ->filter(fn($financial_year) => in_array($financial_year->number, self::IGNORED_YEARS))
            ->pluck('id')
            ->toArray();
    }

    private function filteredFinancialYears()
    {
        return FinancialYear::whereHas('project', fn($q) => $q->where('name', self::PROJECT_NAME))
            ->whereNotIn('id', $this->aggregateYears)
            ->pluck('id')
            ->toArray();
    }

    /**
     * INITIALIZATION FUNCTION (Run Once)
     * Seeds all combinations for ignored years directly into BOTH
     * SystemReport and AggregatedReport tables with structural zeros.
     *
     * Optimized: same logic/order of operations, but:
     *  - IndicatorClass instantiated once per (indicatorClass, period, year, org, crop) — unchanged, this is required
     *    because disaggregation keys can depend on all of those. But indicator lookup is O(1) via keyBy (already was).
     *  - Bulk-fetches existing AggregatedReport parent rows + existing data() rows up front instead of relying purely
     *    on firstOrCreate()'s per-row SELECT+INSERT round trips, cutting query count drastically.
     *  - Wraps each indicatorClass's full sweep in a DB transaction to cut fsync/commit overhead.
     *  - Uses insertOrIgnore-style bulk insert for the structural zero rows instead of per-row firstOrCreate().
     */
    public function initIgnoredYears()
    {
        $indicatorClasses = IndicatorClass::get();
        $reportingPeriods = $this->reporting_period_id ? [$this->reporting_period_id] : ReportingPeriodMonth::pluck('id')->toArray();
        $organisations    = $this->organisation_id ? [$this->organisation_id] : Organisation::pluck('id')->toArray();
        $crops            = CoreFunctions::getCropsWithNull();
        $indicators       = Indicator::get()->keyBy('id');

        foreach ($indicatorClasses as $indicatorClass) {
            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;
            if (! $indicator) {
                continue;
            }

            DB::transaction(function () use ($indicatorClass, $indicator, $reportingPeriods, $organisations, $crops) {
                foreach ($reportingPeriods as $period) {
                    foreach ($this->aggregateYears as $year) {
                        foreach ($organisations as $org) {
                            foreach ($crops as $crop) {
                                try {
                                    $conditions = [
                                        'reporting_period_id' => $period,
                                        'financial_year_id'   => $year,
                                        'organisation_id'     => $org,
                                        'project_id'          => $indicator->project_id,
                                        'indicator_id'        => $indicator->id,
                                        'crop'                => $crop,
                                    ];

                                    // 1. Secure parent record
                                    $aggregatedReport = AggregatedReport::firstOrCreate($conditions);

                                    // 2. Instantiate blueprint class to grab the target disaggregation names
                                    $class = new $indicatorClass->class(
                                        reporting_period: $period,
                                        financial_year: $year,
                                        organisation_id: $org,
                                        enterprise: $crop
                                    );

                                    $disaggregationKeys = array_keys($class->getDisaggregations());

                                    if (empty($disaggregationKeys)) {
                                        continue;
                                    }

                                    // 3. Bulk fetch existing names for this report to avoid per-key firstOrCreate SELECTs
                                    $existingNames = $aggregatedReport->data()
                                        ->whereIn('name', $disaggregationKeys)
                                        ->pluck('name')
                                        ->toArray();

                                    $missingKeys = array_diff($disaggregationKeys, $existingNames);

                                    if (! empty($missingKeys)) {
                                        $now  = Carbon::now();
                                        $rows = array_map(fn($key) => [
                                            'aggregated_report_id' => $aggregatedReport->id,
                                            'name'                 => $key,
                                            'value'                => 0,
                                            'created_at'           => $now,
                                            'updated_at'           => $now,
                                        ], $missingKeys);

                                        // Single bulk insert instead of N firstOrCreate() calls
                                        $aggregatedReport->data()->insert($rows);
                                    }
                                } catch (\Exception $e) {
                                    Log::error("Init Ignored Year Matrix Record Failure: " . $e->getMessage(), [
                                        'indicator_class_id'  => $indicatorClass->id,
                                        'indicator_id'        => $indicator->id,
                                        'reporting_period_id' => $period,
                                        'financial_year_id'   => $year,
                                        'organisation_id'     => $org,
                                        'project_id'          => $indicator->project_id,
                                        'stack_trace'         => $e->getTraceAsString(),
                                    ]);
                                }
                            }
                        }
                    }
                }
            });
        }

        return response()->json(['message' => 'Initialization of ignored years completed successfully.'], 200);
    }

    public function run()
    {
        DB::connection()->disableQueryLog();

        /** ------------------------------------------------------
         * 1. SMART FILTERING
         * ------------------------------------------------------ */
        $indicatorClasses = IndicatorClass::get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::pluck('id')->toArray();

        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();

        $crops = CoreFunctions::getCropsWithNull();

        /** ------------------------------------------------------
         * 2. PROGRESS CALCULATION
         * ------------------------------------------------------ */
        $filteredYears = $this->filteredFinancialYears();
        $allYearsCount = count($filteredYears) + count($this->aggregateYears);

        $this->totalIterations = count($indicatorClasses)
         * count($reportingPeriods)
         * $allYearsCount
         * count($organisations)
         * count($crops);

        $this->current    = 0;
        $this->errorCount = 0;
        $indicators       = Indicator::get()->keyBy('id');

        /** ------------------------------------------------------
         * 3. MAIN LOOP
         * ------------------------------------------------------ */
        foreach ($indicatorClasses as $indicatorClass) {
            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;

            foreach ($reportingPeriods as $period) {
                $this->processYears($filteredYears, $this->aggregateYears, [
                    $indicatorClass,
                    $indicator,
                    $period,
                    $organisations,
                    $crops,
                ]);
            }
        }

        // Surface final error count once the job completes
        Cache::put('report_error_count', $this->errorCount);

        return $this->errorCount;
    }

    private function syncDisaggregations($report, array $disaggregations)
    {
        /** DELETE REMOVED ITEMS + UPSERT ALL IN BULK
         * Replaces the per-key updateOrCreate() loop (N queries) with a single
         * upsert() call, and the delete is unchanged but only runs when needed.
         */
        $existing = $report->data()->pluck('name', 'id')->toArray();
        $toDelete = array_diff($existing, array_keys($disaggregations));

        if (! empty($toDelete)) {
            $report->data()->whereIn('id', array_keys($toDelete))->delete();
        }

        if (empty($disaggregations)) {
            return;
        }

        $now  = Carbon::now();
        $rows = [];
        foreach ($disaggregations as $key => $value) {
            $rows[] = [
                'system_report_id' => $report->id,
                'name'             => $key,
                'value'            => $value,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        // Single upsert instead of N updateOrCreate() calls.
        // Adjust the unique-key column list ([...]) to match your actual DB unique index
        // on the report_data table (commonly ['system_report_id', 'name']).
        $report->data()->getModel()->upsert(
            $rows,
            ['system_report_id', 'name'],
            ['value', 'updated_at']
        );
    }

    private function processCalculations(array $financialYears, array $data, bool $isAggregateYear = false)
    {
        [$indicatorClass, $indicator, $period, $organisations, $crops] = $data;

        foreach ($financialYears as $year) {
            foreach ($organisations as $org) {
                foreach ($crops as $crop) {
                    try {
                        $disaggregations = [];
                        $conditions      = [
                            'reporting_period_id' => $period,
                            'financial_year_id'   => $year,
                            'organisation_id'     => $org,
                            'project_id'          => $indicator->project_id,
                            'indicator_id'        => $indicator->id,
                            'crop'                => $crop,
                        ];

                        // Target destination is ALWAYS SystemReport
                        $systemReport = SystemReport::firstOrCreate($conditions);

                        if ($isAggregateYear) {
                            // 1. Fetch data strictly from the AggregatedReport table location
                            $existingAggregate = AggregatedReport::where($conditions)->first();

                            if ($existingAggregate) {
                                $disaggregations = $existingAggregate->data()->pluck('value', 'name')->toArray();
                            } else {
                                // Fallback explicitly pulls the structural zeros initialized earlier
                                $existingNames = $systemReport->data()->pluck('name')->toArray();

                                if (! empty($existingNames)) {
                                    $disaggregations = array_fill_keys($existingNames, 0);
                                } else {
                                    $class = new $indicatorClass->class(
                                        reporting_period: $period,
                                        financial_year: $year,
                                        organisation_id: $org,
                                        enterprise: $crop
                                    );
                                    $disaggregations = array_fill_keys(array_keys($class->getDisaggregations()), 0);
                                }
                            }
                        } else {
                            // 2. Standard Year: Calculate live from form submissions
                            $class = new $indicatorClass->class(
                                reporting_period: $period,
                                financial_year: $year,
                                organisation_id: $org,
                                enterprise: $crop
                            );
                            $disaggregations = $class->getDisaggregations();
                        }

                        $this->syncDisaggregations($systemReport, $disaggregations);

                    } catch (\Exception $e) {
                        $this->errorCount++;

                        Log::error("Report Error: " . $e->getMessage(), [
                            'reporting_period_id' => $period,
                            'financial_year_id'   => $year,
                            'organisation_id'     => $org,
                            'crop'                => $crop,
                            'indicator_id'        => $indicatorClass->indicator_id,
                            'class'               => $indicatorClass->class,
                            'stack'               => $e->getTraceAsString(),
                        ]);
                    }
                }
            }
        }
    }

    private function processYears(array $financialYears, array $aggregateYears, array $data = [])
    {
        $this->processCalculations($financialYears, $data, false);

        if (count($aggregateYears) > 0) {
            $this->processCalculations($aggregateYears, $data, true);
        }

        $this->current++;
        if ($this->current % 100 === 0) {
            $progress = round(($this->current / $this->totalIterations) * 100 * 0.3);
            Cache::put('report_progress', $progress);
        }
    }
}