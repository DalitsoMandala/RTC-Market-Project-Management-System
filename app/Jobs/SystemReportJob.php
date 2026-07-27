<?php
namespace App\Jobs;

use App\Models\ReportStatus;
use App\Traits\SystemReportsTrait;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    use SystemReportsTrait;

    public $timeout = 3600;
    public $tries   = 3;
    public $backoff = 0;

    public function handle(): void
    {
        $errorCount = $this->runSystemReports();

        if ($errorCount > 0) {
            Log::warning("SystemReportJob completed with {$errorCount} row-level errors.");
        }
    }

    public function failed(\Throwable $exception): void
    {
        ReportStatus::whereKey(1)->update([
            'status'   => 'completed',
            'progress' => 100,
        ]);
        Cache::put('report_progress', 0);
        Cache::put('report_status', 'failed');
        Cache::put('report_progress_error', 'Report update failed: ' . $exception->getMessage());
        Log::error('SystemReportJob failed entirely: ' . $exception->getMessage());
    }
}
