<?php

namespace App\Jobs;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Imports\rtcmarket\HouseholdImport\HrcImport;
use App\Notifications\JobNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class hrcImportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $file;
    public $sheets, $userId;
    /**
     * Create a new job instance.
     */
    public function __construct($file, $sheets, $userId)
    {
        //

        $this->file = $file;
        $this->sheets = $sheets;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //


        # code...
        Excel::import(new HrcImport($this->userId, $this->sheets, $this->file), $this->file);




    }


}