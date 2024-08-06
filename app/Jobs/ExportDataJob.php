<?php

namespace App\Jobs;

use App\Exports\TableExport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportDataJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $fileName;

    public function __construct($data, $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        Excel::store(new TableExport($this->data), 'public/exports/' . $this->fileName . '.xlsx');

    }
}
