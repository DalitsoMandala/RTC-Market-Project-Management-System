<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class TestJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Testing jobs');
        Bus::dispatch(new \App\Jobs\TestJob());
    }
}
