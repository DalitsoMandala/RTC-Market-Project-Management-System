<?php

namespace App\Jobs;

use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\IndicatorClass;
use App\Models\SystemReportData;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\DB;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public $project_id, $financial_year_id, $organisation_id, $indicator_id, $reporting_period_id;
    public function __construct($financial_year_id = null, $project_id = null, $reporting_period_id = null, $organisation_id = null, $indicator_id = null)
    {
        //
        $this->project_id = $project_id;
        $this->financial_year_id = $financial_year_id;
        $this->reporting_period_id = $reporting_period_id;
        $this->organisation_id = $organisation_id;
        $this->indicator_id = $indicator_id;
    }

    /**
     * Execute the job.
     */
    // public function handle(): void

    // {
    //     //


    //     $Indicator_classes = IndicatorClass::all();
    //     foreach ($Indicator_classes as $Indicator_class) {

    //         $reportingPeriods = ReportingPeriodMonth::pluck('id');

    //         $reportingPeriods->push(null);

    //         $financialYears = FinancialYear::pluck('id');
    //         $financialYears->push(null);

    //         $organisations = Organisation::pluck('id');
    //         $organisations->push(null);

    //         foreach ($reportingPeriods as $period) {
    //             foreach ($financialYears as $financialYear) {

    //                 foreach ($organisations as $organisation) {
    //                     //working with ids only

    //                     $class = new $Indicator_class->class(reporting_period: $period, financial_year: $financialYear, organisation_id: $organisation);
    //                     $project = Indicator::find($Indicator_class->indicator_id)->project;
    //                     $report =   SystemReport::create([
    //                         'reporting_period_id' => $period,
    //                         'financial_year_id' => $financialYear,
    //                         'organisation_id' => $organisation,
    //                         'project_id' => $project->id,
    //                         'indicator_id' => $Indicator_class->indicator_id,
    //                         //  'data' => json_encode($class->getDisaggregations())
    //                     ]);

    //                     foreach ($class->getDisaggregations() as $key => $value) {

    //                         $report->data()->create([
    //                             'name' => $key,
    //                             'value' => $value
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //     }


    //     Cache::put('report_', 'completed');
    // }

    public function handle(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;'); // Disable foreign key checks
        SystemReportData::truncate();
        SystemReport::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;'); // Re-enable foreign key checks

        // Fetch Indicator classes and initialize total progress tracking
        $Indicator_classes = IndicatorClass::all();

        // Calculate the total number of iterations to track progress
        $totalReportingPeriods = ReportingPeriodMonth::count() + 1; // +1 for null value
        $totalFinancialYears = FinancialYear::count() + 1; // +1 for null value
        $totalOrganisations = Organisation::count() + 1; // +1 for null value
        $totalIterations = $Indicator_classes->count() * $totalReportingPeriods * $totalFinancialYears * $totalOrganisations;

        // Initialize progress tracking
        $currentProgress = 0;
        Cache::put('report_progress', 0);
        ReportStatus::find(1)->update([
            'status' => 'pending',
            'progress' => 0
        ]);


        foreach ($Indicator_classes as $Indicator_class) {

            $reportingPeriods = ReportingPeriodMonth::pluck('id')->toArray();
            //  $reportingPeriods[] = null; // Push null to the array
            $reportingPeriods = array_chunk($reportingPeriods, 50); // Chunk reporting periods

            $financialYears = FinancialYear::pluck('id')->toArray();
            //    $financialYears[] = null; // Push null to the array
            $financialYears = array_chunk($financialYears, 50); // Chunk financial years

            $organisations = Organisation::pluck('id')->toArray();
            //   $organisations[] = null; // Push null to the array
            $organisations = array_chunk($organisations, 50); // Chunk organisations

            foreach ($reportingPeriods as $periodChunk) {
                foreach ($periodChunk as $period) {

                    foreach ($financialYears as $financialYearChunk) {
                        foreach ($financialYearChunk as $financialYear) {

                            foreach ($organisations as $organisationChunk) {
                                foreach ($organisationChunk as $organisation) {

                                    // Working with ids only
                                    $class = new $Indicator_class->class(
                                        reporting_period: $period,
                                        financial_year: $financialYear,
                                        organisation_id: $organisation
                                    );

                                    $project = Indicator::find($Indicator_class->indicator_id)->project;

                                    $indicators = ResponsiblePerson::where('organisation_id', $organisation)->pluck('indicator_id');

                                    if ($indicators->contains($Indicator_class->indicator_id)) {

                                        $report = SystemReport::create([
                                            'reporting_period_id' => $period,
                                            'financial_year_id' => $financialYear,
                                            'organisation_id' => $organisation,
                                            'project_id' => $project->id,
                                            'indicator_id' => $Indicator_class->indicator_id,
                                            // 'data' => json_encode($class->getDisaggregations())
                                        ]);

                                        // Save disaggregated data
                                        foreach ($class->getDisaggregations() as $key => $value) {
                                            $report->data()->create([
                                                'name' => $key,
                                                'value' => $value
                                            ]);
                                        }
                                    }

                                    // Update progress after processing each organisation
                                    $currentProgress++;
                                    $progressPercentage = ($currentProgress / $totalIterations) * 100;


                                    ReportStatus::find(1)->update([
                                        'status' => 'processing',
                                        'progress' => round($progressPercentage)
                                    ]);

                                    Cache::put('report_progress', round($progressPercentage));
                                }
                            }
                        }
                    }
                }
            }
        }

        ReportStatus::find(1)->update([
            'status' => 'completed',
            'progress' => 100
        ]);

        // Mark the report process as completed
        Cache::put('report_progress', 100);
        Cache::put('report_', 'completed');
    }
}
