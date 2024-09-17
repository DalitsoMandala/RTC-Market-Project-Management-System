<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;
use App\Jobs\ExcelExportJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

trait ExportTrait
{
    //
    public $exporting = false;
    public $exportFinished = false;
    public $exportFailed = false;
    public $exportUniqueId;
    public $batchID;


    public function exportData($tableName)
    {
        $this->exporting = true;
        $this->exportFinished = false;
        $this->exportFailed = false;
        $this->exportUniqueId = Uuid::uuid4()->toString();

        $batch = Bus::batch([
            new ExcelExportJob($tableName, $this->exportUniqueId),
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

    public function download($prefix)
    {
        return Storage::download('public/exports/' . $prefix . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function updateExportProgress()
    {
        $this->exportFinished = $this->exportBatch->finished();

        if ($this->exportFinished && $this->exportBatch->failedJobs === 0) {
            $this->exporting = false;
            $this->exportFailed = false;
        } else if ($this->exportFinished && $this->exportBatch->failedJobs > 0) {
            $this->exporting = false;
            $this->exportFailed = true;
        }
    }
}
