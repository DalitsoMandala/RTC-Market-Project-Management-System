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

trait AggregatedReportsTrait
{
    use SharedReportsHelperTrait;

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

    /**
     * Chunk an array into smaller pieces for memory-efficient processing
     *
     * @param array $array
     * @param int $size
     * @return array
     */
    private function chunkArray(array $array, int $size = 50): array
    {
        return array_chunk($array, $size);
    }

    public function initIgnoredYears()
    {
        $indicatorClasses = IndicatorClass::get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::pluck('id')->toArray();

        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();

        $crops      = CoreFunctions::getCropsWithNull();
        $indicators = Indicator::get()->keyBy('id');

        $reportingPeriods = $this->chunkArray($reportingPeriods);
        $organisations    = $this->chunkArray($organisations);
        $crops            = $this->chunkArray($crops);
        $aggregateYears   = $this->chunkArray($this->aggregateYears);

        foreach ($indicatorClasses as $indicatorClass) {

            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;

            if (! $indicator) {
                continue;
            }

            foreach ($reportingPeriods as $periodChunk) {
                foreach ($periodChunk as $period) {
                    foreach ($aggregateYears as $yearChunk) {
                        foreach ($yearChunk as $year) {
                            foreach ($organisations as $organisationChunk) {
                                foreach ($organisationChunk as $org) {
                                    foreach ($crops as $cropChunk) {
                                        foreach ($cropChunk as $crop) {

                                            try {

                                                DB::transaction(function () use (
                                                    $period,
                                                    $year,
                                                    $org,
                                                    $crop,
                                                    $indicator,
                                                    $indicatorClass
                                                ) {

                                                    $conditions = [
                                                        'reporting_period_id' => $period,
                                                        'financial_year_id'   => $year,
                                                        'organisation_id'     => $org,
                                                        'project_id'          => $indicator->project_id,
                                                        'indicator_id'        => $indicator->id,
                                                        'crop'                => $crop,
                                                    ];

                                                    $aggregatedReport = AggregatedReport::firstOrCreate($conditions);
                                                    $systemReport     = SystemReport::firstOrCreate($conditions);

                                                    $class = new $indicatorClass->class(
                                                        reporting_period: $period,
                                                        financial_year: $year,
                                                        organisation_id: $org,
                                                        enterprise: $crop
                                                    );

                                                    $disaggregationKeys = array_keys(
                                                        $class->getDisaggregations()
                                                    );

                                                    if (empty($disaggregationKeys)) {
                                                        return;
                                                    }

                                                    $now = Carbon::now();

                                                    // Aggregated Report Data
                                                    $existingNames = $aggregatedReport
                                                        ->data()
                                                        ->whereIn('name', $disaggregationKeys)
                                                        ->pluck('name')
                                                        ->toArray();

                                                    $missingKeys = array_diff(
                                                        $disaggregationKeys,
                                                        $existingNames
                                                    );

                                                    if (! empty($missingKeys)) {

                                                        $rows = array_map(
                                                            fn($key) => [
                                                                'aggregated_report_id' => $aggregatedReport->id,
                                                                'name'                 => $key,
                                                                'value'                => 0,
                                                                'created_at'           => $now,
                                                                'updated_at'           => $now,
                                                            ],
                                                            $missingKeys
                                                        );

                                                        $aggregatedReport
                                                            ->data()
                                                            ->insert($rows);
                                                    }

                                                    // System Report Data
                                                    $existingSysNames = $systemReport
                                                        ->data()
                                                        ->whereIn('name', $disaggregationKeys)
                                                        ->pluck('name')
                                                        ->toArray();

                                                    $missingSysKeys = array_diff(
                                                        $disaggregationKeys,
                                                        $existingSysNames
                                                    );

                                                    if (! empty($missingSysKeys)) {

                                                        $rows = array_map(
                                                            fn($key) => [
                                                                'system_report_id' => $systemReport->id,
                                                                'name'             => $key,
                                                                'value'            => 0,
                                                                'created_at'       => $now,
                                                                'updated_at'       => $now,
                                                            ],
                                                            $missingSysKeys
                                                        );

                                                        $systemReport
                                                            ->data()
                                                            ->insert($rows);
                                                    }

                                                });

                                            } catch (\Exception $e) {

                                                Log::error(
                                                    "Init Ignored Year Matrix Record Failure: " . $e->getMessage(),
                                                    [
                                                        'reporting_period_id' => $period,
                                                        'financial_year_id'   => $year,
                                                        'organisation_id'     => $org,
                                                        'crop'                => $crop,
                                                        'indicator_id'        => $indicatorClass->indicator_id,
                                                        'class'               => $indicatorClass->class,
                                                    ]
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Initialization of ignored years completed successfully.',
        ], 200);
    }
    public function runAggregatedReports(): int
    {

        if (empty($this->aggregateYears)) {
            return 0;
        }
        $indicatorClasses = IndicatorClass::get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::pluck('id')->toArray();

        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();

        $crops = CoreFunctions::getCropsWithNull();

        // Calculate BEFORE chunking
        $this->totalIterations = $indicatorClasses->count()
         * count($reportingPeriods)
         * count($this->aggregateYears)
         * count($organisations)
         * count($crops);

        // Chunk afterwards
        $reportingPeriods = $this->chunkArray($reportingPeriods);
        $organisations    = $this->chunkArray($organisations);
        $crops            = $this->chunkArray($crops);
        $aggregateYears   = $this->chunkArray($this->aggregateYears);

        $this->current    = 0;
        $this->errorCount = 0;

        $indicators = Indicator::get()->keyBy('id');

        foreach ($indicatorClasses as $indicatorClass) {

            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;

            if (! $indicator) {
                continue;
            }

            $this->processAggregatedYears($aggregateYears, [
                $indicatorClass,
                $indicator,
                $reportingPeriods,
                $organisations,
                $crops,
            ]);
        }

        Cache::put('report_error_count', $this->errorCount);

        return $this->errorCount;
    }

    private function processAggregatedYears(array $financialYearChunks, array $data)
    {
        [
            $indicatorClass,
            $indicator,
            $periodChunks,
            $organisationChunks,
            $cropChunks,
        ] = $data;

        foreach ($periodChunks as $periodChunk) {
            foreach ($periodChunk as $period) {
                foreach ($financialYearChunks as $financialYearChunk) {
                    foreach ($financialYearChunk as $year) {
                        foreach ($organisationChunks as $organisationChunk) {
                            foreach ($organisationChunk as $org) {
                                foreach ($cropChunks as $cropChunk) {
                                    foreach ($cropChunk as $crop) {

                                        try {

                                            DB::transaction(function () use (
                                                $period,
                                                $year,
                                                $org,
                                                $indicator,
                                                $crop,
                                                $indicatorClass
                                            ) {

                                                $conditions = [
                                                    'reporting_period_id' => $period,
                                                    'financial_year_id'   => $year,
                                                    'organisation_id'     => $org,
                                                    'project_id'          => $indicator->project_id,
                                                    'indicator_id'        => $indicator->id,
                                                    'crop'                => $crop,
                                                ];

                                                $systemReport = SystemReport::firstOrCreate($conditions);

                                                $existingAggregate = AggregatedReport::where($conditions)->first();

                                                if ($existingAggregate) {

                                                    $disaggregations = $existingAggregate
                                                        ->data()
                                                        ->pluck('value', 'name')
                                                        ->toArray();

                                                } else {

                                                    $existingNames = $systemReport
                                                        ->data()
                                                        ->pluck('name')
                                                        ->toArray();

                                                    if (! empty($existingNames)) {

                                                        $disaggregations = array_fill_keys(
                                                            $existingNames,
                                                            0
                                                        );

                                                    } else {

                                                        $class = new $indicatorClass->class(
                                                            reporting_period: $period,
                                                            financial_year: $year,
                                                            organisation_id: $org,
                                                            enterprise: $crop
                                                        );

                                                        $disaggregations = array_fill_keys(
                                                            array_keys($class->getDisaggregations()),
                                                            0
                                                        );
                                                    }
                                                }

                                                $this->syncDisaggregations(
                                                    $systemReport,
                                                    $disaggregations
                                                );

                                            });

                                        } catch (\Exception $e) {

                                            $this->errorCount++;

                                            Log::error(
                                                "Aggregate Report Run Failure: " . $e->getMessage(),
                                                [
                                                    'reporting_period_id' => $period,
                                                    'financial_year_id'   => $year,
                                                    'organisation_id'     => $org,
                                                    'crop'                => $crop,
                                                    'indicator_id'        => $indicatorClass->indicator_id,
                                                    'class'               => $indicatorClass->class,
                                                ]
                                            );
                                        }

                                        $this->updateProgress(capMin: 50, capMax: 100);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
