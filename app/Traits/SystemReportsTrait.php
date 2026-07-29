<?php
namespace App\Traits;

use App\Helpers\CoreFunctions;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorClass;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\SystemReport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait SystemReportsTrait
{
    use SharedReportsHelperTrait;

    public function __construct(
        public readonly ?int $financial_year_id = null,
        public readonly ?int $project_id = null,
        public readonly ?int $reporting_period_id = null,
        public readonly ?int $organisation_id = null,
        public readonly ?int $indicator_id = null,
    ) {}
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
    private function filteredFinancialYears(): array
    {
        $this->aggregateYears = FinancialYear::whereHas('project', fn($q) => $q->where('name', self::PROJECT_NAME))
            ->get()
            ->filter(fn($financial_year) => ! in_array($financial_year->number, self::IGNORED_YEARS))
            ->pluck('id')
            ->toArray();

        return $this->aggregateYears;
    }
    public function runSystemReports(): int
    {
        $indicatorClasses = IndicatorClass::with('indicator')->whereHas('indicator', fn($q) => $q->where('is_active', true))->get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::where('type', '!=', 'UNSPECIFIED')->pluck('id')->toArray();
        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();
        $crops         = CoreFunctions::getCropsWithNull();
        $filteredYears = $this->filteredFinancialYears();

        // ✅ Compute total iterations BEFORE chunking (using actual counts)
        $this->totalIterations = $indicatorClasses->count()
         * count($reportingPeriods)
         * count($filteredYears)
         * count($organisations)
         * count($crops);

        // Chunk arrays for memory efficiency
        $reportingPeriods = $this->chunkArray($reportingPeriods);
        $organisations    = $this->chunkArray($organisations);
        $crops            = $this->chunkArray($crops);
        $filteredYears    = $this->chunkArray($filteredYears);

        $this->current    = 0;
        $this->errorCount = 0;
        $indicators       = Indicator::get()->keyBy('id');

        foreach ($indicatorClasses as $indicatorClass) {
            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;
            if (! $indicator) {
                continue;
            }

            $this->processSystemYears($filteredYears, [
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

    private function processSystemYears(array $financialYearChunks, array $data)
    {
        [$indicatorClass, $indicator, $periodChunks, $organisationChunks, $cropChunks] = $data;

        foreach ($periodChunks as $periodChunk) {
            foreach ($periodChunk as $period) {
                foreach ($financialYearChunks as $financialYearChunk) {
                    foreach ($financialYearChunk as $year) {
                        foreach ($organisationChunks as $organisationChunk) {
                            foreach ($organisationChunk as $org) {

                                // Only query once per organisation
                                $currentResponsible = ResponsiblePerson::where([
                                    'organisation_id' => $org,
                                    'indicator_id'    => $indicatorClass->indicator_id,
                                ])->exists();

                                foreach ($cropChunks as $cropChunk) {
                                    foreach ($cropChunk as $crop) {

                                        // Preserve historical reports
                                        $hasExistingReport = SystemReport::where([
                                            'financial_year_id'   => $year,
                                            'reporting_period_id' => $period,
                                            'organisation_id'     => $org,
                                            'indicator_id'        => $indicator->id,
                                            'crop'                => $crop,
                                            'project_id'          => $indicator->project_id,
                                        ])->exists();

                                        // Skip only if the organisation is neither currently
                                        // responsible nor has historical data for this combination
                                        if (! $currentResponsible && ! $hasExistingReport) {
                                            $this->updateProgress(capMin: 0, capMax: 50); // 👈 Fixed: Added caps
                                            continue;
                                        }
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

                                                $class = new $indicatorClass->class(
                                                    reporting_period: $period,
                                                    financial_year: $year,
                                                    organisation_id: $org,
                                                    enterprise: $crop
                                                );

                                                $this->syncDisaggregations(
                                                    $systemReport,
                                                    $class->getDisaggregations()
                                                );

                                            });
                                        } catch (\Exception $e) {
                                            $this->errorCount++;

                                            Log::error("System Report Run Failure: " . $e->getMessage(), [
                                                'reporting_period_id' => $period,
                                                'financial_year_id'   => $year,
                                                'organisation_id'     => $org,
                                                'crop'                => $crop,
                                                'indicator_id'        => $indicatorClass->indicator_id,
                                                'class'               => $indicatorClass->class,
                                                'stack'               => $e->getTraceAsString(),
                                            ]);

                                        }
                                        // Always update progress
                                        $this->updateProgress(capMin: 0, capMax: 50);
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