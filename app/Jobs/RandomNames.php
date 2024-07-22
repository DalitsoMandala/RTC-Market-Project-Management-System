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

class RandomNames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        $data = [];
        $faker = Faker::create();
        $total = 25;

        foreach (range(1, $total) as $index) {
            $data[$faker->name] = $faker->country;

        }
    }
}
