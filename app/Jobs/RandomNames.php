<?php

namespace App\Jobs;

use App\Models\JobProgress;
use Faker\Factory as Faker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RandomNames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $progressKey2;

    public function __construct($progressKey2)
    {
        $this->progressKey2 = $progressKey2;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        $data = [];
        $faker = Faker::create();
        $total = 1000;

        for ($index = 1; $index <= $total; $index++) {
            sleep(2);
            // Simulate work by generating random data
            $data[$faker->name] = $faker->country;
            $calculation = ($index / $total) * 100;
            // Update progress in the cache
            Cache::put($this->progressKey2, $calculation);

        }

        // Ensure the progress is set to 100% when the job is complete
        Cache::put($this->progressKey2, 100);
    }
}
