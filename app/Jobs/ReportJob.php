<?php
namespace App\Jobs;

use App\Traits\ReportsTrait;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    use ReportsTrait;
    public $timeout = 3600; // tune to worst-case runtime
    public $tries   = 1;    // avoid duplicate runs on retry
    public $backoff = 0;

    public function __construct(
        ?int $financial_year_id = null,
        ?int $project_id = null,
        ?int $reporting_period_id = null,
        ?int $organisation_id = null,
        ?int $indicator_id = null,
    ) {
        // ReportsTrait's constructor sets readonly props + aggregateYears
        $this->initReportsTrait(
            $financial_year_id,
            $project_id,
            $reporting_period_id,
            $organisation_id,
            $indicator_id,
        );
    }

    public function handle(): void
    {
        $errorCount = $this->run();

        if ($errorCount > 0) {
            Log::warning("GenerateReportsJob completed with {$errorCount} row-level errors.");
            // e.g. notify an admin here if $errorCount exceeds a threshold
        }
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put('report_progress', 0);
        Log::error('GenerateReportsJob failed entirely: ' . $exception->getMessage());
    }

}