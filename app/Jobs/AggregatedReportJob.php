<?php
namespace App\Jobs;

use App\Models\ReportStatus;
use App\Traits\AggregatedReportsTrait;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AggregatedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    use AggregatedReportsTrait;

    public $timeout = 3600;
    public $tries   = 3;
    public $backoff = 0;

    public function handle(): void
    {
        $errorCount = $this->runAggregatedReports();

        if ($errorCount > 0) {
            Log::warning("AggregatedReportJob completed with {$errorCount} row-level errors.");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put('report_progress', 0);
        Log::error('AggregatedReportJob failed entirely: ' . $exception->getMessage());
        ReportStatus::whereKey(1)->update([
            'status'   => 'completed',
            'progress' => 100,
        ]);
    }
}
