<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet1;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet2;
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
        'RTC_FARMERS',
        'RTC_FARM_FLUP',
        'RTC_FARM_AGR',
        'RTC_FARM_DOM',
        'RTC_FARM_MARKETS',
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
            new RpmFarmerImportSheet1($this->userId, $this->file),
            new RpmFarmerImportSheet2($this->userId, $this->file),
            new RpmFarmerImportSheet3($this->userId, $this->file),
            new RpmFarmerImportSheet4($this->userId, $this->file),
            new RpmFarmerImportSheet5($this->userId, $this->file),
        ];
    }
    public function registerEvents(): array
    {
        return [
                // Handle by a closure.
            BeforeImport::class => function (BeforeImport $event) {
                // dd($event);
                $diff = ImportValidateHeading::validateHeadings($this->sheetNames, $this->expectedSheetNames);

                if (count($diff) > 0) {
                    session()->flash('error-import', "File contains invalid sheets!");
                    throw new UserErrorException("File contains invalid sheets!");

                }
            },

        ];
    }
}