<?php

namespace App\Jobs;


use App\Models\Project;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use Illuminate\Bus\Queueable;
use App\Models\AdditionalReport;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AdditionalReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

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

        //This is to add additional report
        // Fetch all indicators with their disaggregations
        $indicators = Indicator::with('disaggregations')->get();

        // Process system reports in chunks to avoid memory overload
        SystemReport::with(['data'])->chunk(100, function ($systemReports) use ($indicators) {
            foreach ($systemReports as $systemReport) {
                $projectId = $systemReport->project_id;
                $indicatorId = $systemReport->indicator_id;
                $reportingPeriodId = $systemReport->reporting_period_id;
                $financialYearId = $systemReport->financial_year_id;
                $organisationId = $systemReport->organisation_id;

                // Find the indicator for this report
                $indicator = $indicators->where('id', $indicatorId)->first();

                if (!$indicator) {
                    continue; // Skip if the indicator is not found
                }

                // Get all disaggregations for this indicator
                $disaggregations = $indicator->disaggregations;

                // Process the system report data
                $systemReport->data->each(function ($item) use (
                    $indicatorId,
                    $reportingPeriodId,
                    $organisationId,
                    $disaggregations,
                    $financialYearId
                ) {
                    // Find the matching disaggregation
                    $indicatorDisaggregate = $disaggregations->where('name', $item->name)->first();

                    if (!$indicatorDisaggregate) {
                        Log::info("Indicator with name '{$item->name}' not found");
                        return; // Skip if no matching disaggregation
                    }

                    // Fetch additional report data in chunks
                    AdditionalReport::where('indicator_id', $indicatorId)
                        ->where('period_month_id', $reportingPeriodId)
                        ->where('organisation_id', $organisationId)
                        ->where('indicator_disaggregation_id', $indicatorDisaggregate->id)
                        ->chunk(50, function ($additionalReports) use ($item, $financialYearId) {
                            foreach ($additionalReports as $additionalData) {
                                // Update value based on financial year
                                if (FinancialYear::find($financialYearId)->number == 1) {
                                    $item->update(['value' => $item->value + $additionalData->year_1]);
                                } elseif (FinancialYear::find($financialYearId)->number == 2) {
                                    $item->update(['value' => $item->value + $additionalData->year_2]);
                                }
                            }
                        });
                });
            }
        });
    }
}
