<?php

namespace App\Console\Commands;

use App\Jobs\RunBackupJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class RunBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database to local file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        RunBackupJob::dispatch();
    }


}
