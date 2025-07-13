<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ReportStatus;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use App\Helpers\PopulatePreviousValue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PopulatePreviousValueJob implements ShouldQueue
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
        //

        $class = new PopulatePreviousValue();
        $project = Project::where('name', 'RTC MARKET')->first();
        $project ? $class->start($project->id) : null; // percentages
        Cache::put('report_progress', 66);
        ReportStatus::find(1)->update([
            'status'   => 'pending',
            'progress' => 66,
        ]);
    }
}
