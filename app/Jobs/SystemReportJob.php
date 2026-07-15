<?php
namespace App\Jobs;

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
    public $tries   = 4;
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
        Cache::put('report_progress', 0);
        Log::error('SystemReportJob failed entirely: ' . $exception->getMessage());
    }
}
