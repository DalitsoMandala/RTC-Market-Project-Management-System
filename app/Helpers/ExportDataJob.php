<?php

namespace App\Helpers;

use App\Jobs\ExcelExportJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class ExportDataJob
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
    public function export()
    {


        $this->exporting = true;
        $this->exportFinished = false;
        $this->exportFailed = false;
        $id = Str::random();
        $this->exportUniqueId = $id;
        $batch = Bus::batch([
            new ExcelExportJob($this->Modelname, $id),
        ])->dispatch();

        $this->batchID = $batch->id;

    }


    public function getExportBatchProperty()
    {
        if (!$this->batchID) {
            return null;
        }

        return Bus::findBatch($this->batchID);
    }

    public function updateExportProgress()
    {
        $batch = $this->getExportBatchProperty();

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
