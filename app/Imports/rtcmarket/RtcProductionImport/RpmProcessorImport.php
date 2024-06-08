<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Helpers\ImportValidateHeading;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet2;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet3;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet4;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\BeforeImport;

class RpmProcessorImport implements WithMultipleSheets
{
    use Importable, RegistersEventListeners;

    public $userId;
    public $sheetNames = [];

    public $file;
    protected $expectedSheetNames = [
        'RTC_PROCESSORS',
        'RTC_PROC_FLUP',
        'RTC_PROC_DOM',
        'RTC_PROC_AGREEMENT',
        'RTC_PROC_MARKETS',
    ];

    public function __construct($userId, $sheets, $file = null)
    {
        $this->userId = $userId;
        $this->sheetNames = $sheets;
        $this->file = $file;
    }

    public function sheets(): array
    {
        return [

            new RpmProcessorImportSheet1($this->userId, $this->file),
            new RpmProcessorImportSheet2($this->userId, $this->file),
            new RpmProcessorImportSheet3($this->userId, $this->file),
            new RpmProcessorImportSheet4($this->userId, $this->file),
            new RpmProcessorImportSheet5($this->userId, $this->file),
        ];
    }
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeImport::class => function (BeforeImport $event) {
                $diff = ImportValidateHeading::validateHeadings($this->sheetNames, $this->expectedSheetNames);

                if (count($diff) > 0) {
                    session()->flash('error-import', "File contains invalid sheets!");
                    throw new Exception("File contains invalid sheets!");

                }
            },

        ];
    }
}
