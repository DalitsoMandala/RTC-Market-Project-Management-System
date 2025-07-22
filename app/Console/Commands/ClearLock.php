<?php

namespace App\Console\Commands;

use App\Models\ReportStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Cache Lock and Update Report Status';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info('Clearing Cache Lock...');

        Cache::forget('report_lock');  //



    }
}
