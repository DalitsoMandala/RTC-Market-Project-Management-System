<?php

namespace App\Jobs;

use Log;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ReportStatus;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\IndicatorClass;
use App\Models\SystemReportData;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\DB;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Cache;
use App\Helpers\PopulatePreviousValue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\PercentageIncreaseIndicator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $project_id, $financial_year_id, $organisation_id, $indicator_id, $reporting_period_id;

    public function __construct($financial_year_id = null, $project_id = null, $reporting_period_id = null, $organisation_id = null, $indicator_id = null)
    {
        $this->project_id = $project_id;
        $this->financial_year_id = $financial_year_id;
        $this->reporting_period_id = $reporting_period_id;
        $this->organisation_id = $organisation_id;
        $this->indicator_id = $indicator_id;
    }

    // public function handle(): void
    // {
    //     DB::statement('SET FOREIGN_KEY_CHECKS = 0;'); // Disable foreign key checks for truncation
    //     SystemReportData::truncate();
    //     SystemReport::truncate();
    //     PercentageIncreaseIndicator::truncate();
    //     DB::statement('SET FOREIGN_KEY_CHECKS = 1;'); // Re-enable foreign key checks

    //     // Fetch Indicator classes and set up progress tracking
    //     $Indicator_classes = IndicatorClass::all();
    //     $totalReportingPeriods = ReportingPeriodMonth::count() + 1;
    //     $totalFinancialYears = FinancialYear::count() + 1;
    //     $totalOrganisations = Organisation::count() + 1;
    //     $totalIterations = $Indicator_classes->count() * $totalReportingPeriods * $totalFinancialYears * $totalOrganisations;

    //     $currentProgress = 0;
    //     $updateInterval = 10; // Update progress every 10 iterations
    //     Cache::put('report_progress', 0);
    //     ReportStatus::find(1)->update([
    //         'status' => 'pending',
    //         'progress' => 0
    //     ]);

    //     foreach ($Indicator_classes as $Indicator_class) {
    //         $reportingPeriods = ReportingPeriodMonth::pluck('id')->toArray();
    //         $reportingPeriods = array_chunk($reportingPeriods, 50); // Chunk reporting periods

    //         $financialYears = FinancialYear::pluck('id')->toArray();
    //         $financialYears = array_chunk($financialYears, 50); // Chunk financial years

    //         $organisations = Organisation::pluck('id')->toArray();
    //         $organisations = array_chunk($organisations, 50); // Chunk organisations

    //         foreach ($reportingPeriods as $periodChunk) {
    //             foreach ($periodChunk as $period) {
    //                 foreach ($financialYears as $financialYearChunk) {
    //                     foreach ($financialYearChunk as $financialYear) {
    //                         foreach ($organisations as $organisationChunk) {
    //                             foreach ($organisationChunk as $organisation) {
    //                                 try {
    //                                     $class = new $Indicator_class->class(
    //                                         reporting_period: $period,
    //                                         financial_year: $financialYear,
    //                                         organisation_id: $organisation
    //                                     );

    //                                     $project = Indicator::find($Indicator_class->indicator_id)->project;
    //                                     $indicators = ResponsiblePerson::where('organisation_id', $organisation)->pluck('indicator_id');

    //                                     if ($indicators->contains($Indicator_class->indicator_id)) {
    //                                         $report = SystemReport::create([
    //                                             'reporting_period_id' => $period,
    //                                             'financial_year_id' => $financialYear,
    //                                             'organisation_id' => $organisation,
    //                                             'project_id' => $project->id,
    //                                             'indicator_id' => $Indicator_class->indicator_id,
    //                                         ]);

    //                                         foreach ($class->getDisaggregations() as $key => $value) {
    //                                             $report->data()->create([
    //                                                 'name' => $key,
    //                                                 'value' => $value
    //                                             ]);
    //                                         }
    //                                     }

    //                                     // Update progress
    //                                     $currentProgress++;
    //                                     if ($currentProgress % $updateInterval === 0 || $currentProgress === $totalIterations) {
    //                                         $progressPercentage = ($currentProgress / $totalIterations) * 100;
    //                                         Cache::put('report_progress', round($progressPercentage));
    //                                         ReportStatus::find(1)->update([
    //                                             'status' => 'processing',
    //                                             'progress' => round($progressPercentage)
    //                                         ]);
    //                                     }
    //                                 } catch (\Exception $e) {
    //                                     Log::error("Error processing report for organisation: $organisation", ['error' => $e->getMessage()]);
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     $class = new PopulatePreviousValue();

    //     $class->start(); // percentages


    //     ReportStatus::find(1)->update([
    //         'status' => 'completed',
    //         'progress' => 100
    //     ]);
    //     Cache::put('report_progress', 100);
    //     Cache::put('report_', 'completed');
    // }

    public function handle(): void
    {
        // Fetch Indicator classes and set up progress tracking
        $Indicator_classes = IndicatorClass::all();
        $totalReportingPeriods = ReportingPeriodMonth::count() + 1;
        $totalFinancialYears = FinancialYear::count() + 1;
        $totalOrganisations = Organisation::count() + 1;
        $totalIterations = $Indicator_classes->count() * $totalReportingPeriods * $totalFinancialYears * $totalOrganisations;

        $currentProgress = 0;
        $updateInterval = 10; // Update progress every 10 iterations
        Cache::put('report_progress', 0);
        ReportStatus::find(1)->update([
            'status' => 'pending',
            'progress' => 0
        ]);

        foreach ($Indicator_classes as $Indicator_class) {
            $reportingPeriods = ReportingPeriodMonth::pluck('id')->toArray();
            $reportingPeriods = array_chunk($reportingPeriods, 50); // Chunk reporting periods

            $financialYears = FinancialYear::first()->pluck('id')->toArray();
            $financialYears = array_chunk($financialYears, 50); // Chunk financial years

            $organisations = Organisation::pluck('id')->toArray();
            $organisations = array_chunk($organisations, 50); // Chunk organisations

            foreach ($reportingPeriods as $periodChunk) {
                foreach ($periodChunk as $period) {
                    foreach ($financialYears as $financialYearChunk) {
                        foreach ($financialYearChunk as $financialYear) {
                            foreach ($organisations as $organisationChunk) {
                                foreach ($organisationChunk as $organisation) {
                                    try {
                                        $class = new $Indicator_class->class(
                                            reporting_period: $period,
                                            financial_year: $financialYear,
                                            organisation_id: $organisation
                                        );

                                        $project = Indicator::find($Indicator_class->indicator_id)->project;
                                        $indicators = ResponsiblePerson::where('organisation_id', $organisation)->pluck('indicator_id');

                                        if ($indicators->contains($Indicator_class->indicator_id)) {
                                            // Use updateOrCreate to handle updates
                                            $report = SystemReport::updateOrCreate(
                                                [
                                                    'reporting_period_id' => $period,
                                                    'financial_year_id' => $financialYear,
                                                    'organisation_id' => $organisation,
                                                    'project_id' => $project->id,
                                                    'indicator_id' => $Indicator_class->indicator_id,
                                                ],
                                                [] // No additional fields to update
                                            );

                                            foreach ($class->getDisaggregations() as $key => $value) {
                                                // Update or create data entries
                                                $report->data()->updateOrCreate(
                                                    ['name' => $key],
                                                    ['value' => $value]
                                                );
                                            }
                                        }

                                        // Update progress
                                        $currentProgress++;
                                        if ($currentProgress % $updateInterval === 0 || $currentProgress === $totalIterations) {
                                            $progressPercentage = ($currentProgress / $totalIterations) * 100;
                                            Cache::put('report_progress', round($progressPercentage));
                                            ReportStatus::find(1)->update([
                                                'status' => 'processing',
                                                'progress' => round($progressPercentage)
                                            ]);
                                        }
                                    } catch (\Exception $e) {
                                        Log::error("Error processing report for organisation: $organisation", ['error' => $e->getMessage()]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $class = new PopulatePreviousValue();

        $class->start(); // percentages


        ReportStatus::find(1)->update([
            'status' => 'completed',
            'progress' => 100
        ]);
        Cache::put('report_progress', 100);
        Cache::put('report_', 'completed');
    }
}