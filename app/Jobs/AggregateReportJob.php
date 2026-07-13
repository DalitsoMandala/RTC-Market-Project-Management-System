<?php

namespace App\Jobs;

use App\Models\AggregatedReport;
use App\Models\SystemReportData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AggregateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 1200; // 20 minutes
    public $backoff = [60, 300, 600]; // Retry delays
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Use chunkById() on the Query Builder (not the collection)
        // 2. Use with() to eager-load the systemReport relationship
        SystemReportData::with(['systemReport', 'systemReport.indicator', 'systemReport.indicator.disaggregations'])->chunkById(1000, function ($reportDataCollection) {

            // Use a database transaction to speed up insertions dramatically
            DB::transaction(function () use ($reportDataCollection) {
                foreach ($reportDataCollection as $reportData) {

                    // Safety check to prevent throwing errors if the relationship is missing
                    if (!$reportData->systemReport) {
                        continue;
                    }

                    $name = $reportData->name;

                    $indicator = $reportData->systemReport?->indicator;

                    if (!$indicator) {
                        continue;
                    }

                    $disaggregation = $indicator->disaggregations
                        ->firstWhere('name', $name);

                    if ($disaggregation) {
                        // The name exists
                        $indicatorId = $indicator->id;
                        $disaggregationId = $disaggregation->id;
                        $reportingPeriodId = $reportData->systemReport->reporting_period_id;
                        $financialYearId = $reportData->systemReport->financial_year_id;
                        $organisationId = $reportData->systemReport->organisation_id;
                        $crop = $reportData->systemReport->crop;
                        $projectId = $reportData->systemReport->project_id;
                        AggregatedReport::updateOrCreate(
                            [
                                'indicator_id' => $indicatorId,
                                'indicator_disaggregation_id' => $disaggregationId,
                                'reporting_period_id' => $reportingPeriodId,
                                'financial_year_id' => $financialYearId,
                                'organisation_id' => $organisationId,
                                'crop' => $crop,
                                'project_id' => $projectId
                            ],
                            [
                                'value' => 0
                            ]
                        );
                    }
                }
            });
        });

        // Moved this outside the catch block so it only logs on actual success
        Log::info('Aggregated Report Data Processing Completed Successfully.');
    }
}
