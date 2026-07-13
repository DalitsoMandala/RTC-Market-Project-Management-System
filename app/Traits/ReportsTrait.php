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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait ReportsTrait
{
    //

    protected array $aggregateYears = [];
    const IGNORED_YEARS             = [1, 2, 3];
    const PROJECT_NAME              = 'RTC MARKET';

    protected $current         = 0;
    protected $totalIterations = 0;

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
            ->get()
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

                                // 1. Secure parent records for BOTH tables
                                //   $systemReport     = SystemReport::firstOrCreate($conditions);
                                $aggregatedReport = AggregatedReport::firstOrCreate($conditions);

                                // 2. Instantiate blueprint class to grab the target disaggregation names
                                $class = new $indicatorClass->class(
                                    reporting_period: $period,
                                    financial_year: $year,
                                    organisation_id: $org,
                                    enterprise: $crop
                                );

                                $disaggregationKeys = array_keys($class->getDisaggregations());

                                // 3. Safe zero-fill structural inserts for both models
                                foreach ($disaggregationKeys as $key) {
                                    // Seed System Report Data
                                    // $systemReport->data()->firstOrCreate(
                                    //     ['name' => $key],
                                    //     ['value' => 0]
                                    // );

                                    // Seed Aggregated Report Data
                                    $aggregatedReport->data()->firstOrCreate(
                                        ['name' => $key],
                                        ['value' => 0]
                                    );
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
        }

        return response()->json(['message' => 'Initialization of ignored years completed successfully.'], 200);

    }

    public function run()
    {
        /** ------------------------------------------------------
         * 1. SMART FILTERING
         * ------------------------------------------------------ */
        $indicatorClasses = IndicatorClass::get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::get()->pluck('id')->toArray();

        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();

        $crops = CoreFunctions::getCropsWithNull();

        /** ------------------------------------------------------
         * 2. PROGRESS CALCULATION
         * ------------------------------------------------------ */
        $allYearsCount = count($this->filteredFinancialYears()) + count($this->aggregateYears);

        $this->totalIterations = count($indicatorClasses)
         * count($reportingPeriods)
         * $allYearsCount
         * count($organisations)
         * count($crops);

        $this->current = 0;
        $indicators    = Indicator::get()->keyBy('id');

        /** ------------------------------------------------------
         * 3. MAIN LOOP
         * ------------------------------------------------------ */
        foreach ($indicatorClasses as $indicatorClass) {
            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;

            foreach ($reportingPeriods as $period) {
                $this->processYears($this->filteredFinancialYears(), $this->aggregateYears, [
                    $indicatorClass,
                    $indicator,
                    $period,
                    $organisations,
                    $crops,
                ]);
            }
        }
    }

    private function syncDisaggregations($report, array $disaggregations)
    {
        /** DELETE REMOVED ITEMS */
        $existing = $report->data()->pluck('name')->toArray();
        $toDelete = array_diff($existing, array_keys($disaggregations));

        if (! empty($toDelete)) {
            $report->data()->whereIn('name', $toDelete)->delete();
        }

        /** UPSERT ALL DISAGGREGATIONS */
        foreach ($disaggregations as $key => $value) {
            $report->data()->updateOrCreate(
                ['name' => $key],
                ['value' => $value]
            );
        }
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