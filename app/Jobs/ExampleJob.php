<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ExampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $progressKey;

    public function __construct($progressKey)
    {
        $this->progressKey = $progressKey;
    }

    public function handle()
    {
        Cache::flush();
        $totalSteps = 10000;
        for ($i = 1; $i <= $totalSteps; $i++) {
            // Simulate work
            sleep(2);
            $calculation = ($i / $totalSteps) * 100;

            // Report progress
            Cache::put($this->progressKey, $calculation);
        }

        // Job completed
        Cache::put($this->progressKey, 100);
    }
}
