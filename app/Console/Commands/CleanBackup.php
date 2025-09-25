<?php

namespace App\Console\Commands;

use App\Jobs\CleanBackupJob;
use Illuminate\Console\Command;

class CleanBackup extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        CleanBackupJob::dispatch();
    }
}
