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
        //
        $this->info('Updating information...');
        Cache::put('report_progress', 0);

        // Chain the jobs
        Bus::chain([
            new ReportJob(),
            new PopulatePreviousValueJob(),
            new AdditionalReportJob(),
            function () {
                ReportStatus::find(1)->update([
                    'status' => 'completed',
                    'progress' => 100
                ]);
                Cache::put('report_progress', 100);
                Cache::put('report_', 'completed');
            }
        ])->catch(function (\Throwable $e) {
            // Handle any exceptions that occur during the chain
            logger()->error('Job chain failed: ' . $e->getMessage());


            ReportStatus::find(1)->update([
                'status' => 'completed',
                'progress' => 100
            ]);
            Cache::put('report_progress', 100);
            Cache::put('report_', 'completed');
        })->dispatch();
    }
}