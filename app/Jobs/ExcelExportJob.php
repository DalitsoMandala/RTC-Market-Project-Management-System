<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\rtcmarket\HrcExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\rtcmarket\HouseholdExport\ExportData;


class ExcelExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    public $name;
    public $uniqueID;
    /**
     * Create a new job instance.
     */
    public function __construct($name, $uniqueID)
    {
        //

        $this->name = $name;
        $this->uniqueID = $uniqueID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->name === 'hrc') {

            Excel::store(new ExportData, 'public/exports/household-rtc-consumption_' . $this->uniqueID . '.xlsx');

        }
    }
}
