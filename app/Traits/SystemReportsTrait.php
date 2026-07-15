<?php
namespace App\Traits;

use App\Helpers\CoreFunctions;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorClass;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
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

    private function filteredFinancialYears(): array
    {
        return FinancialYear::whereHas('project', fn($q) => $q->where('name', self::PROJECT_NAME))
            ->whereNotIn('id', $this->aggregateYears)
            ->pluck('id')
            ->toArray();
    }

    public function runSystemReports(): int
    {
        DB::connection()->disableQueryLog();

        $indicatorClasses = IndicatorClass::get();
        $reportingPeriods = $this->reporting_period_id ? [$this->reporting_period_id] : ReportingPeriodMonth::where('type', '!=', 'UNSPECIFIED')->pluck('id')->toArray();
        $organisations    = $this->organisation_id ? [$this->organisation_id] : Organisation::pluck('id')->toArray();
        $crops            = CoreFunctions::getCropsWithNull();
        $filteredYears    = $this->filteredFinancialYears();

        // Total iteration calculation remains perfectly aligned with the unified nested loop below
        $this->totalIterations = count($indicatorClasses) * count($reportingPeriods) * count($filteredYears) * count($organisations) * count($crops);
        $this->current         = 0;
        $this->errorCount      = 0;
        $indicators            = Indicator::get()->keyBy('id');

        foreach ($indicatorClasses as $indicatorClass) {
            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;
            if (! $indicator) {
                continue;
            }

            // Clean delegation: Pass all structural arrays down to be processed cleanly
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

    private function processSystemYears(array $financialYears, array $data)
    {
        [$indicatorClass, $indicator, $reportingPeriods, $organisations, $crops] = $data;

        foreach ($reportingPeriods as $period) {
            foreach ($financialYears as $year) {
                foreach ($organisations as $org) {
                    foreach ($crops as $crop) {
                        DB::transaction(function () use ($period, $year, $org, $indicator, $crop, $indicatorClass) {
                            try {
                                $conditions = [
                                    'reporting_period_id' => $period,
                                    'financial_year_id'   => $year,
                                    'organisation_id'     => $org,
                                    'project_id'          => $indicator->project_id,
                                    'indicator_id'        => $indicator->id,
                                    'crop'                => $crop,
                                ];

                                $systemReport = SystemReport::updateOrCreate($conditions);

                                $class = new $indicatorClass->class(
                                    reporting_period: $period,
                                    financial_year: $year,
                                    organisation_id: $org,
                                    enterprise: $crop
                                );

                                $this->syncDisaggregations($systemReport, $class->getDisaggregations());

                            } catch (\Exception $e) {
                                $this->errorCount++;
                                Log::error("System Report Run Failure: " . $e->getMessage(), [
                                    'reporting_period_id' => $period,
                                    'financial_year_id'   => $year,
                                    'organisation_id'     => $org,
                                    'crop'                => $crop,
                                    'indicator_id'        => $indicatorClass->indicator_id,
                                    'class'               => $indicatorClass->class,
                                ]);
                                throw $e;
                            }
                        });

                        $this->updateProgress();
                    }
                }
            }
        }
    }
}
