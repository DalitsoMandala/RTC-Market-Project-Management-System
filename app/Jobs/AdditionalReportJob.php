<?php

namespace App\Jobs;


use App\Models\Project;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ReportStatus;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\AdditionalReport;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AdditionalReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
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

        //This is to add additional report
        // Fetch all indicators with their disaggregations
        $indicators = Indicator::with('disaggregations')->get();
        $indicators = Indicator::with('disaggregations')->get();
        $reportingPeriodId = ReportingPeriodMonth::where('type', 'UNSPECIFIED')->pluck(
            'id'
        );

        // Process system reports in chunks to avoid memory overload
        SystemReport::with(['data'])->where('reporting_period_id', $reportingPeriodId)->chunk(100, function ($systemReports) use ($indicators) {

            foreach ($systemReports as $systemReport) {
                $projectId = $systemReport->project_id;
                $indicatorId = $systemReport->indicator_id;
                $reportingPeriodId = $systemReport->reporting_period_id;
                $financialYearId = $systemReport->financial_year_id;
                $organisationId = $systemReport->organisation_id;
                $crop = $systemReport->crop;

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
                    $financialYearId,
                    $crop
                ) {
                    // Find the matching disaggregation
                    $indicatorDisaggregate = $disaggregations->where('name', $item->name)
                        ->where('indicator_id', $indicatorId)
                        ->first();

                    if (!$indicatorDisaggregate) {
                        return; // Skip if the disaggregation is not found
                    }


                    // Fetch additional report data in chunks
                    $financialYear = FinancialYear::find($financialYearId);

                    if ($financialYear) {
                        $yearColumn = match ($financialYear->number) {
                            1 => 'year_1',
                            2 => 'year_2',
                            3 => 'year_3',
                            4 => 'year_4',
                            default => null,
                        };

                        if ($yearColumn) {
                            $totalValue = AdditionalReport::where('indicator_id', $indicatorId)
                                ->where('period_month_id', $reportingPeriodId)
                                ->where('organisation_id', $organisationId)
                                ->where('crop', $crop)
                                ->where('indicator_disaggregation_id', $indicatorDisaggregate->id)
                                ->sum($yearColumn);

                            $item->update(['value' => $item->value + $totalValue]);
                        }
                    }
                });
            }
        });


        Cache::put('report_progress', 99);
        ReportStatus::find(1)->update([
            'status'   => 'pending',
            'progress' => 99,
        ]);
    }
}
