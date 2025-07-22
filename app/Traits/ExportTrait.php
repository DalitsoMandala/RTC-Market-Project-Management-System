<?php

namespace App\Traits;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use App\Jobs\ExcelExportJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ExportTrait
{
    public $batchID;
    public $exporting = false;
    public $exportFinished = false;

    public $exportFailed = false;

    public $exportUniqueId = '';

    public $progress = 0;

    public $Modelname;


    public function execute($Modelname)
    {
        $this->Modelname = $Modelname;
    }
    public function performExport()
    {

        $user = User::find(auth()->user()->id);
        $this->exporting = true;
        $this->exportFinished = false;
        $this->exportFailed = false;
        $id = Str::random();
        $this->exportUniqueId = $id;
        $batch = Bus::batch([
            new ExcelExportJob($this->Modelname, $id, $user),
        ])->dispatch();

        $this->batchID = $batch->id;
    }


    public function BatchProperty()
    {
        if (!$this->batchID) {
            return null;
        }

        return Bus::findBatch($this->batchID);
    }

    public function failedJobs() {}

    public function exportProgress()
    {
        $batch = $this->BatchProperty();

        // If batch is found, check for progress and update status
        if ($batch) {
            $this->progress = $batch->progress();  // Update progress

            if ($batch->finished()) {
                $this->exporting = false;
                $this->exportFinished = true;
                $this->exportFailed = $batch->failedJobs > 0;

                if ($this->exportFailed) {
                    $this->failedJobErrors = [];

                    foreach ($batch->failedJobIds as $failedJobId) {
                        $failedJob = DB::table('failed_jobs')->where('uuid', $failedJobId)->first();

                        if ($failedJob) {
                            // Collect exception messages for UI or further handling
                            $this->failedJobErrors[] = [
                                'uuid' => $failedJob->uuid,
                                'exception' => $failedJob->exception,
                            ];

                            // Optionally log for developers
                            Log::error("Export batch job failed: {$failedJob->uuid}", [
                                'exception' => $failedJob->exception,
                                'payload' => $failedJob->payload,
                            ]);
                        }
                    }
                }
                $this->dispatch('download-export');
            }
        }
    }
}
