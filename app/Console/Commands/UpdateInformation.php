<?php

namespace App\Console\Commands;

use App\Helpers\PopulatePreviousValue;
use App\Jobs\AdditionalReportJob;
use App\Jobs\MarketReportJob;

use App\Jobs\PopulatePreviousValueJob;
use App\Jobs\ReportJob;
use App\Jobs\SyncronizeTableJob;
use App\Models\ReportStatus;
use Illuminate\Bus\Batch;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

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
        Artisan::call('clear-lock');
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
            // Fresh run
            $this->info("Starting fresh report job chain...");
            $this->resetReportStatus($reportStatus);

            $this->runReportJobs();
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

    private function runReportJobs()
    {
           $reportStatus = ReportStatus::find(1);
        Bus::chain([
            new SyncronizeTableJob(),
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
