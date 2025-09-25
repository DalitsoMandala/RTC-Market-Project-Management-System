<?php

namespace App\Console\Commands;

use App\Jobs\ReportJob;
use Illuminate\Bus\Batch;
use App\Models\ReportStatus;

use App\Jobs\MarketReportJob;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Jobs\AdditionalReportJob;
use Illuminate\Support\Facades\Bus;

use Illuminate\Support\Facades\Cache;
use App\Helpers\PopulatePreviousValue;
use App\Jobs\PopulatePreviousValueJob;

class UpdateInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:information';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update system reports';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        ini_set('max_execution_time', 0);
        $this->info('Checking report status...');

        // Optional: prevent parallel executions
        $lock = Cache::lock('report_lock', 3600); // lock for 1 hour

        if (!$lock->get()) {
            $this->warn('Another instance is already processing the report.');
            return;
        }

        try {
            $reportStatus = ReportStatus::find(1);

            if (!$reportStatus) {
                $this->error('ReportStatus with ID 1 not found.');
                return;
            }



            $lastUpdate = $reportStatus->updated_at;
            $isStale = Carbon::parse($lastUpdate)->diffInDays(Carbon::now()) > 1 && $reportStatus->status === 'pending';

            if ($isStale) {
                $this->info("Stale report detected. Resetting...");
                $this->resetReportStatus($reportStatus);
                $this->runReportJobs($reportStatus);
                return;
            }

            if ($reportStatus->status === 'pending') {
                $this->comment("Report is already running at {$reportStatus->progress}%.");
                Cache::put('report_progress', $reportStatus->progress);
                Cache::put('report_status', 'pending');
                return;
            }

            // Fresh run
            $this->info("Starting fresh report job chain...");
            $this->resetReportStatus($reportStatus);
            $this->runReportJobs($reportStatus);
        } finally {
            $lock->release();
        }
    }

    private function resetReportStatus($reportStatus)
    {
        $reportStatus->update([
            'status' => 'pending',
            'progress' => 0,
        ]);
        Cache::put('report_progress', 0);
        Cache::put('report_status', 'pending');
    }

    private function runReportJobs($reportStatus)
    {
        Bus::chain([
            new ReportJob(),
            new PopulatePreviousValueJob(),
            new AdditionalReportJob(),
            new MarketReportJob(),
            function () use ($reportStatus) {
                $reportStatus->update([
                    'status' => 'completed',
                    'progress' => 100,
                ]);
                Cache::put('report_progress', 100);
                Cache::put('report_status', 'completed');
            }
        ])
            ->catch(function (\Throwable $e) use ($reportStatus) {
                logger()->error('Report job chain failed: ' . $e->getMessage());

                $reportStatus->update([
                    'status' => 'completed',
                    'progress' => 100,
                ]);
                Cache::put('report_progress', 100);
                Cache::put('report_status', 'completed');
            })
            ->dispatch();
    }
}
