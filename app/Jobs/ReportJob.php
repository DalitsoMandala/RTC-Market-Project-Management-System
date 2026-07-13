<?php
namespace App\Jobs;

use App\Traits\ReportsTrait;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    use ReportsTrait;
    public $tries   = 3;
    public $timeout = 1200;
    public $backoff = [60, 300, 600];

    //

    public function handle()
    {
        $this->run();
    }

}