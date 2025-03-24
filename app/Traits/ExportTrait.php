<?php

namespace App\Traits;

use App\Jobs\ExcelExportJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

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
            }
        }
    }
}