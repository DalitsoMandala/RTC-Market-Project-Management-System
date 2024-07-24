<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartQueueWorker extends Command
{
    protected $signature = 'queue:start-worker';
    protected $description = 'Start the queue worker';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting queue worker...');
        exec('php artisan queue:work');
    }
}