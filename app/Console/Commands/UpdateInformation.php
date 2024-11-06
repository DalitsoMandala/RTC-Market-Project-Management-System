<?php

namespace App\Console\Commands;

use App\Jobs\ReportJob;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

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
            new ReportJob([])
        ])->before(function (Batch $batch) {
            // The batch has been created but no jobs have been added...

        })->progress(function (Batch $batch) {
            // A single job has completed successfully...
        })->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, \Throwable $e) {
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...

            $this->info('Information updated successfully!');

        })

            ->dispatch();



    }
}
