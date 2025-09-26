<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if Laravel schedule is working properly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = '✅ Scheduler executed at: ' . now();

        // log to storage/logs/laravel.log


        // also show in terminal if run manually
        $this->info($message);
    }
}
