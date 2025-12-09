<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncronizeTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        //

        try {

            $ReportingPeriodMonths = \App\Models\ReportingPeriodMonth::with(['submissions', 'reportingPeriod'])->get();
            foreach ($ReportingPeriodMonths as $ReportingPeriodMonth) {
                if ($ReportingPeriodMonth->submissions->isNotEmpty()) {
                    $submissions = $ReportingPeriodMonth->submissions;
                    $submissionsByTable = $submissions->groupBy('table_name');

                    foreach ($submissionsByTable as $tableName => $tableSubmissions) {
                        // Process in chunks to prevent memory issues with large datasets
                        $tableSubmissions->chunk(100)->each(function ($chunk) use ($tableName, $ReportingPeriodMonth) {


                            $batchNos = $chunk->pluck('batch_no')->toArray();
                            $batchNosWithDisapprovals = $chunk->where('status', 'denied')->pluck('batch_no')->toArray();

                            DB::table($tableName)
                                ->whereIn('uuid', $batchNosWithDisapprovals)
                                ->delete();



                            DB::table($tableName)
                                ->whereIn('uuid', $batchNos)
                                ->where('status', 'approved')
                                ->update(['period_month_id' => $ReportingPeriodMonth->id]);
                        });
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('SyncronizeTableJob failed: ' . $e->getMessage());
        }
    }
}
