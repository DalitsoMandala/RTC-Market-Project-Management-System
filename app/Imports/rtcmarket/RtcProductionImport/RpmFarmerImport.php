<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Helpers\ImportValidateHeading;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet1;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet2;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet3;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet4;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet5;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\BeforeImport;

class RpmFarmerImport implements WithMultipleSheets, WithEvents
{
    use Importable, RegistersEventListeners;

    public $userId;
    public $sheetNames = [];

    public $file;
    protected $expectedSheetNames = [
        'RTC PROD. FARMERS', 'RTC PROD. FOLLOW UP', 'RTC PROD. CONC_AGR', 'RTC PROD. DOM_MARKETS', 'RTC PROD. INTER_MARKETS',
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
            'RTC PROD. FARMERS' => new RpmFarmerImportSheet1($this->userId, $this->file),
            'RTC PROD. FOLLOW UP' => new RpmFarmerImportSheet2($this->userId, $this->file),
            'RTC PROD. CONC_AGR' => new RpmFarmerImportSheet3($this->userId, $this->file),
            'RTC PROD. DOM_MARKETS' => new RpmFarmerImportSheet4($this->userId, $this->file),
            'RTC PROD. INTER_MARKETS' => new RpmFarmerImportSheet5($this->userId, $this->file),
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
