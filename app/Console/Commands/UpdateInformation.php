<?php

namespace App\Console\Commands;

use App\Jobs\ReportJob;
use Illuminate\Bus\Batch;
use App\Models\ReportStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use App\Helpers\PopulatePreviousValue;
use App\Jobs\AdditionalReportJob;
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

        // Acquire a lock to prevent duplicate execution
        //   $lock = Cache::lock('report_lock', 3600); // 1 hour lock



        $reportStatus = ReportStatus::find(1);

        if ($reportStatus && $reportStatus->status === 'pending') {
            $this->comment("Report is already running at {$reportStatus->progress}%.");
            Cache::put('report_progress', $reportStatus->progress);
            Cache::put('report_', 'pending');

            return;
        }

        // Reset the report status
        $reportStatus->update([
            'status' => 'pending',
            'progress' => 0,
        ]);
        Cache::put('report_progress', 0);
        Cache::put('report_', 'pending');

        // Chain the jobs
        Bus::chain([
            new ReportJob(),
            new PopulatePreviousValueJob(),
            new AdditionalReportJob(),
            function () use ($reportStatus) {
                $reportStatus->update([
                    'status' => 'completed',
                    'progress' => 100,
                ]);
                Cache::put('report_progress', 100);
                Cache::put('report_', 'completed');
            }
        ])
            ->catch(function (\Throwable $e) use ($reportStatus) {
                logger()->error('Report job chain failed: ' . $e->getMessage());

                $reportStatus->update([
                    'status' => 'completed',
                    'progress' => 100,
                ]);
                Cache::put('report_progress', 100);
                Cache::put('report_', 'completed');
            })
            // Optional: you can omit ->onQueue('default') since it's default anyway
            ->dispatch();
    }
}
