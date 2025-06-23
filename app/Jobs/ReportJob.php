<?php

namespace App\Jobs;

use App\Helpers\CoreFunctions;
use App\Models\Crop;
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
    public $timeout = 1200;           // 20 minutes
    public $backoff = [60, 300, 600]; // Retry delays

    public $project_id, $financial_year_id, $organisation_id, $indicator_id, $reporting_period_id;

    public function __construct($financial_year_id = null, $project_id = null, $reporting_period_id = null, $organisation_id = null, $indicator_id = null)
    {
        $this->project_id          = $project_id;
        $this->financial_year_id   = $financial_year_id;
        $this->reporting_period_id = $reporting_period_id;
        $this->organisation_id     = $organisation_id;
        $this->indicator_id        = $indicator_id;
    }

    public function handle(): void
    {

        // Fetch Indicator classes and set up progress tracking
        $Indicator_classes     = IndicatorClass::all();
        $totalReportingPeriods = ReportingPeriodMonth::count() + 1;
        $totalFinancialYears   = FinancialYear::count() + 1;
        $totalOrganisations    = Organisation::count() + 1;
        $totalIterations       = $Indicator_classes->count() * $totalReportingPeriods * $totalFinancialYears * $totalOrganisations;

        $currentProgress = 0;
        $updateInterval  = 10; // Update progress every 10 iterations



        foreach ($Indicator_classes as $Indicator_class) {
            $reportingPeriods = ReportingPeriodMonth::pluck('id')->toArray();
            $reportingPeriods = array_chunk($reportingPeriods, 50); // Chunk reporting periods

            $financialYears = FinancialYear::first()->pluck('id')->toArray();
            $financialYears = array_chunk($financialYears, 50); // Chunk financial years

            $organisations = Organisation::pluck('id')->toArray();
            $organisations = array_chunk($organisations, 50); // Chunk organisations

            $crops = CoreFunctions::getCropsWithNull();
            $crops = array_chunk($crops, 50);

            foreach ($reportingPeriods as $periodChunk) {
                foreach ($periodChunk as $period) {
                    foreach ($financialYears as $financialYearChunk) {
                        foreach ($financialYearChunk as $financialYear) {
                            foreach ($organisations as $organisationChunk) {
                                foreach ($organisationChunk as $organisation) {
                                    foreach ($crops as $cropChunk) {
                                        foreach ($cropChunk as $crop) {

                                            try {

                                                //its class is \App\Helpers\rtcmarket\indicators
                                                $class = new $Indicator_class->class(
                                                    reporting_period: $period,
                                                    financial_year: $financialYear,
                                                    organisation_id: $organisation,
                                                    enterprise: $crop
                                                );

                                                $project    = Indicator::find($Indicator_class->indicator_id)->project;
                                                $indicators = ResponsiblePerson::where('organisation_id', $organisation)->pluck('indicator_id');

                                                if ($indicators->contains($Indicator_class->indicator_id)) {
                                                    // Use updateOrCreate to handle updates

                                                    $report = SystemReport::updateOrCreate(
                                                        [
                                                            'reporting_period_id' => $period,
                                                            'financial_year_id'   => $financialYear,
                                                            'organisation_id'     => $organisation,
                                                            'project_id'          => $project->id,
                                                            'indicator_id'        => $Indicator_class->indicator_id,
                                                            'crop'                => $crop,
                                                        ],
                                                        [
                                                            'reporting_period_id' => $period,
                                                            'financial_year_id'   => $financialYear,
                                                            'organisation_id'     => $organisation,
                                                            'project_id'          => $project->id,
                                                            'indicator_id'        => $Indicator_class->indicator_id,
                                                            'crop'                => $crop,
                                                        ]
                                                    );

                                                    $disaggregations = $class->getDisaggregations();

                                                    // Delete removed disaggregations
                                                    $existing = $report->data()->pluck('name')->toArray();
                                                    $new      = array_keys($disaggregations);
                                                    $toDelete = array_diff($existing, $new);

                                                    if (! empty($toDelete)) {
                                                        $report->data()->whereIn('name', $toDelete)->delete();
                                                    }

                                                    // Update or create disaggregations using your method
                                                    foreach ($disaggregations as $key => $value) {
                                                        $this->updateDisaggregations($report, $key, $value);
                                                    }
                                                }
                                            } catch (\Exception $e) {

                                                Log::error($e->getMessage(), $e->getTrace());
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

        Cache::put('report_progress', 33);
        ReportStatus::find(1)->update([
            'status'   => 'pending',
            'progress' => 33,
        ]);
    }

    public function updateDisaggregations($report, $key, $value)
    {
        $reportData = $report->data()->updateOrCreate(
            ['name' => $key],
            ['value' => $value]
        );

        return $reportData;
    }
}
