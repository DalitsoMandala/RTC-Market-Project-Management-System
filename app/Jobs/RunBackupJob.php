<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Cache::put('backup_progress', 0);
        Cache::put('backup_error', null);


        try {
            // Run backup (example: only DB)
            Artisan::call('backup:run --only-db');
            sleep(10);
            // Mark progress as done
            Cache::put('backup_progress', 100);
        } catch (\Exception $e) {
            Cache::put('backup_error', 'Something went wrong during the backup');
            Cache::put('backup_progress', 100);
            Log::error("Backup failed: " . $e->getMessage());
        }
    }
}
