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
                $submissions = $ReportingPeriodMonth->submissions->isNotEmpty() ? $ReportingPeriodMonth->submissions : [];
                if (count($submissions) > 1) {
                    foreach ($submissions as $submission) {
                        $uuid = $submission->batch_no;
                        $tableName = $submission->table_name;

                        if ($submission->status == 'denied') {

                            DB::table($tableName)->where('uuid', $uuid)->delete();
                        } else if ($submission->status == 'approved') {
                            DB::table($tableName)->where('uuid', $uuid)->update([
                                'period_month_id' => $ReportingPeriodMonth->id

                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('SyncronizeTableJob failed: ' . $e->getMessage());
        }
    }
}
