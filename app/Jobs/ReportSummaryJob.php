<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Exports\Reports\ReportSheet;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReportSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        $cacheKey = 'progress_summary_' . $this->user->id; // Unique cache key
        Cache::put($cacheKey, 0);

        try {

            $filename = 'project_progress_report_' . $this->user->id . '_' . Str::random(10) . '.xlsx'; // Unique filename
            Excel::store(new ReportSheet($this->user), $filename, 'public');
            Cache::put($cacheKey, 100);
            // Optionally, notify the user of success
        } catch (\Exception $e) {
            Cache::put($cacheKey, 100);
            Log::error("Report generation failed for user {$this->user->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
