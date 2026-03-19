<?php

namespace App\Jobs;

use App\Helpers\CoreFunctions;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorClass;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use App\Models\ResponsiblePerson;
use App\Models\SystemReport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $tries   = 3;
    public $timeout = 1200;
    public $backoff = [60, 300, 600];

    public $project_id;
    public $financial_year_id;
    public $organisation_id;
    public $indicator_id;
    public $reporting_period_id;

    public function __construct(
        $financial_year_id = null,
        $project_id = null,
        $reporting_period_id = null,
        $organisation_id = null,
        $indicator_id = null
    ) {
        $this->project_id          = $project_id;
        $this->financial_year_id   = $financial_year_id;
        $this->reporting_period_id = $reporting_period_id;
        $this->organisation_id     = $organisation_id;
        $this->indicator_id        = $indicator_id;
    }

    public function handle(): void
    {
        /** ------------------------------------------------------
         * 1. SMART FILTERING (BEST PRACTICE)
         * ------------------------------------------------------ */
        $indicatorClasses = IndicatorClass::get();

        $reportingPeriods = $this->reporting_period_id
            ? [$this->reporting_period_id]
            : ReportingPeriodMonth::where('type','!=', 'UNSPECIFIED')->pluck('id')->toArray();

        $financialYears = $this->financial_year_id
            ? [$this->financial_year_id]
            : FinancialYear::pluck('id')->toArray();

        $organisations = $this->organisation_id
            ? [$this->organisation_id]
            : Organisation::pluck('id')->toArray();

        $crops = CoreFunctions::getCropsWithNull(); // already array


        /** ------------------------------------------------------
         * 2. PROGRESS CALCULATION
         * ------------------------------------------------------ */
        $totalIterations =
            count($indicatorClasses)
            * count($reportingPeriods)
            * count($financialYears)
            * count($organisations)
            * count($crops);

        $current = 0;

        $indicators = Indicator::get()->keyBy('id');

        /** ------------------------------------------------------
         * 3. MAIN LOOP (optimized – no dangerous chunk nesting)
         * ------------------------------------------------------ */
        foreach ($indicatorClasses as $indicatorClass) {

            $indicator = $indicators[$indicatorClass->indicator_id] ?? null;
            foreach ($reportingPeriods as $period) {
                foreach ($financialYears as $year) {
                    foreach ($organisations as $org) {

                        // Load once per organisation
                        $indicatorPermissions = ResponsiblePerson::where('organisation_id', $org)
                            ->pluck('indicator_id');

                        if (! $indicatorPermissions->contains($indicatorClass->indicator_id)) {
                            continue; // Organisation not allowed for this indicator
                        }

                        foreach ($crops as $crop) {

                            try {
                                /** Instantiate indicator helper ONCE per combination */


                                $class = new $indicatorClass->class(
                                    reporting_period: $period,
                                    financial_year: $year,
                                    organisation_id: $org,
                                    enterprise: $crop
                                );

                                /**
                                 * FIXED: First check if report exists for this combination
                                 * This replaces your incomplete code
                                 */

                                // Check if a report already exists for this combination
                                $existingReport = SystemReport::where([
                                    'reporting_period_id' => $period,
                                    'financial_year_id' => $year,
                                    'organisation_id' => $org,
                                    'project_id' => $indicator->project_id,
                                    'indicator_id' => $indicator->id,
                                    'crop' => $crop,
                                ])->first();

                                /** MAIN REPORT RECORD - Create only if it doesn't exist */
                                $report = $existingReport ?? SystemReport::create([
                                    'reporting_period_id' => $period,
                                    'financial_year_id'   => $year,
                                    'organisation_id'     => $org,
                                    'project_id'          => $indicator->project_id,
                                    'indicator_id'        => $indicator->id,
                                    'crop'                => $crop,
                                ]);

                                /** GET DISAGGREGATIONS */
                                $disaggregations = $class->getDisaggregations();

                                /** DELETE REMOVED ITEMS */
                                $existing = $report->data()->pluck('name')->toArray();
                                $toDelete = array_diff($existing, array_keys($disaggregations));

                                if ($toDelete) {
                                    $report->data()->whereIn('name', $toDelete)->delete();
                                }

                                /** UPSERT ALL DISAGGREGATIONS */
                                foreach ($disaggregations as $key => $value) {
                                    $report->data()->updateOrCreate(
                                        ['name' => $key],
                                        ['value' => $value]
                                    );
                                }
                            } catch (\Exception $e) {
                                Log::error("Report Error: " . $e->getMessage());
                            }

                            /** PROGRESS */
                            $current++;
                            if ($current % 100 === 0) {
                                // Multiply the total progress by 0.3 to cap the "sum up" at 30%
                                $progress = round(($current / $totalIterations) * 100 * 0.3);
                                Cache::put('report_progress', $progress);
                            }
                        }
                    }
                }
            }
        }
    }
}
