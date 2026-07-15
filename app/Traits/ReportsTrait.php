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

    public function __construct(
        public readonly ?int $financial_year_id = null,
        public readonly ?int $project_id = null,
        public readonly ?int $reporting_period_id = null,
        public readonly ?int $organisation_id = null,
        public readonly ?int $indicator_id = null,
    ) {
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

                                    // Also ensure SystemReport exists for consistency
                                    $systemReport = SystemReport::firstOrCreate($conditions);

                                    // 2. Instantiate blueprint class to grab target disaggregation names
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

                                    // 3. Bulk insert for both tables
                                    $now = Carbon::now();

                                    // For AggregatedReport
                                    $existingNames = $aggregatedReport->data()
                                        ->whereIn('name', $disaggregationKeys)
                                        ->pluck('name')
                                        ->toArray();

                                    $missingKeys = array_diff($disaggregationKeys, $existingNames);

                                    if (! empty($missingKeys)) {
                                        $rows = array_map(fn($key) => [
                                            'aggregated_report_id' => $aggregatedReport->id,
                                            'name'                 => $key,
                                            'value'                => 0,
                                            'created_at'           => $now,
                                            'updated_at'           => $now,
                                        ], $missingKeys);

                                        $aggregatedReport->data()->insert($rows);
                                    }

                                    // For SystemReport - ensure structural zeros exist
                                    $existingSysNames = $systemReport->data()
                                        ->whereIn('name', $disaggregationKeys)
                                        ->pluck('name')
                                        ->toArray();

                                    $missingSysKeys = array_diff($disaggregationKeys, $existingSysNames);

                                    if (! empty($missingSysKeys)) {
                                        $sysRows = array_map(fn($key) => [
                                            'system_report_id' => $systemReport->id,
                                            'name'             => $key,
                                            'value'            => 0,
                                            'created_at'       => $now,
                                            'updated_at'       => $now,
                                        ], $missingSysKeys);

                                        $systemReport->data()->insert($sysRows);
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

                                    throw $e; // Rollback transaction
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

        $indicatorClasses = IndicatorClass::get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::pluck('id')->toArray();

        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();

        $crops = CoreFunctions::getCropsWithNull();

        $filteredYears = $this->filteredFinancialYears();
        $allYearsCount = count($filteredYears) + count($this->aggregateYears);

        // Calculate total iterations correctly
        $this->totalIterations = count($indicatorClasses)
         * count($reportingPeriods)
         * $allYearsCount
         * count($organisations)
         * count($crops);

        $this->current    = 0;
        $this->errorCount = 0;
        $indicators       = Indicator::get()->keyBy('id');

        foreach ($indicatorClasses as $indicatorClass) {
            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;
            if (! $indicator) {
                continue;
            }

            foreach ($reportingPeriods as $period) {
                // Process filtered years and aggregate years separately
                if (! empty($filteredYears)) {
                    $this->processYears($filteredYears, [
                        $indicatorClass,
                        $indicator,
                        $period,
                        $organisations,
                        $crops,
                    ], false);
                }

                if (! empty($this->aggregateYears)) {
                    $this->processYears($this->aggregateYears, [
                        $indicatorClass,
                        $indicator,
                        $period,
                        $organisations,
                        $crops,
                    ], true);
                }
            }
        }

        // Surface final error count once the job completes
        Cache::put('report_error_count', $this->errorCount);

        return $this->errorCount;
    }

    private function processYears(array $financialYears, array $data, bool $isAggregateYear = false)
    {
        [$indicatorClass, $indicator, $period, $organisations, $crops] = $data;

        foreach ($financialYears as $year) {
            foreach ($organisations as $org) {
                foreach ($crops as $crop) {
                    // DB Transaction ensures absolute consistency
                    DB::transaction(function () use ($period, $year, $org, $indicator, $crop, $isAggregateYear, $indicatorClass) {
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
                                    // Fallback explicitly pulls structural zeros initialized earlier
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
                                'is_aggregate_year'   => $isAggregateYear,
                                'stack'               => $e->getTraceAsString(),
                            ]);

                            // Force rollback of the active transaction
                            throw $e;
                        }
                    });
                }
            }
        }

        // Update progress after processing all combinations in this year group
        $this->current++;
        if ($this->current % 100 === 0) {
            $progress = round(($this->current / $this->totalIterations) * 100);
            Cache::put('report_progress', $progress);
        }
    }

    /**
     * FIXED: Sync disaggregations with proper foreign key handling
     */
    private function syncDisaggregations($report, array $disaggregations)
    {
        if (empty($disaggregations)) {
            return;
        }

        // Get existing records
        $existing = $report->data()->pluck('name', 'id')->toArray();
        $toDelete = array_diff($existing, array_keys($disaggregations));

        if (! empty($toDelete)) {
            $report->data()->whereIn('id', array_keys($toDelete))->delete();
        }

        // Prepare upsert data with foreign key
        $now        = Carbon::now();
        $upsertData = [];

        foreach ($disaggregations as $key => $value) {
            $upsertData[] = [
                'system_report_id' => $report->id, // CRITICAL: Include the foreign key
                'name'             => $key,
                'value'            => $value,
                'created_at'       => $now, // Include created_at for new records
                'updated_at'       => $now,
            ];
        }

        // Use upsert with composite unique key
        $report->data()->upsert(
            $upsertData,
            ['system_report_id', 'name'], // Composite unique constraint
            ['value', 'updated_at']       // Fields to update on conflict
        );
    }
}
