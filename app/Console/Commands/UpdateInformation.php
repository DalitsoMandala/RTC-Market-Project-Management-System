<?php
namespace App\Console\Commands;

use App\Jobs\AggregatedReportJob;
use App\Jobs\SystemReportJob;
use App\Models\ReportStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        $reportStatus = ReportStatus::find(1);

        if (! $reportStatus) {
            $this->error('ReportStatus with ID 1 not found.');
            return;
        }

        if ($reportStatus->status === 'processing') {
            $this->warn('A report update is already running — skipping this run.');
            Cache::put('report_progress_error', 'A report update is already running — skipping this run.');
            return;
        }

        Cache::put('report_progress_error', null);

        $reportStatus->update([
            'status'   => 'processing',
            'progress' => 0,
        ]);

        Cache::put('report_progress', 0);
        Cache::put('report_status', 'processing');

        $this->info("Report status: {$reportStatus->status}, progress: {$reportStatus->progress}%");
        $this->info("Starting fresh report job chain...");

        $this->runReportJobs();
    }

    private function runReportJobs()
    {
        $reportStatus = ReportStatus::find(1);
        Bus::chain([

            new SystemReportJob(),     // Run live calculations
            new AggregatedReportJob(), // Run aggregate calculations

            function () use ($reportStatus) {
                $reportStatus->update([
                    'status'   => 'completed',
                    'progress' => 100,
                ]);
                Cache::put('report_progress', 100);
                Cache::put('report_status', 'completed');
            },
        ])
            ->catch(function (\Throwable $e) use ($reportStatus) {
                logger()->error('Report job chain failed: ' . $e->getMessage());

                $reportStatus->update([
                    'status'   => 'failed',
                    'progress' => 0,
                ]);
                Cache::put('report_progress', 0);
                Cache::put('report_status', 'failed');
                Cache::put('report_progress_error', 'Report update failed: ' . $e->getMessage());
            })
            ->onQueue('reports')
            ->dispatch();
    }

    public function clearReportLock()
    {
        $this->info('Clearing Cache Lock...');
        $reportStatus = ReportStatus::find(1);

        if ($reportStatus) {
            $reportStatus->update([
                'status'   => 'processing',
                'progress' => 0,
            ]);

            Cache::put('report_progress', 100);
            Cache::put('report_status', 'completed');
            Artisan::call('queue:clear --queue=reports');
            $this->info('Cache Lock Cleared.');
        }

    }
    public function failed(\Throwable $exception): void
    {
        Cache::put('report_progress', 0);
        Log::error('UpdateInformation command failed entirely: ' . $exception->getMessage());
    }
}
