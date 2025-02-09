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
        Bus::batch([
            new ReportJob(),
            new PopulatePreviousValueJob(),
            new AdditionalReportJob()

        ])->before(function (Batch $batch) {
            // The batch has been created but no jobs have been added...

        })->progress(function (Batch $batch) {
            // A single job has completed successfully...
        })->then(function (Batch $batch) {
            // All jobs completed successfully...

            ReportStatus::find(1)->update([
                'status' => 'completed',
                'progress' => 100
            ]);
            Cache::put('report_progress', 100);
            Cache::put('report_', 'completed');
        })->catch(function (Batch $batch, \Throwable $e) {
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...

        })

            ->dispatch();
    }
}
