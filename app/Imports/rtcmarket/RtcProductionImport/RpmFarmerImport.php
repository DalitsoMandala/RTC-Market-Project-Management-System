<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet1;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet2;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet3;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet4;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImportSheet5;
use App\Jobs\sendtoTableJob;
use App\Models\HouseholdRtcConsumption;
use App\Models\ImportError;
use App\Models\JobProgress;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RpmFarmerDomMarket;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\JobNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;

class RpmFarmerImport implements WithMultipleSheets, WithEvents, ShouldQueue
{
    use Importable, RegistersEventListeners;


    protected $expectedSheetNames = [
        'RTC_FARMERS',
        'RTC_FARM_FLUP',
        'RTC_FARM_AGR',
        'RTC_FARM_DOM',
        'RTC_FARM_MARKETS',
    ];
    public $location, $userId, $file;
    public $sheetNames = [];


    public $totalRows;
    public $uuid;
    public $submissionData = [];
    public function __construct($userId, $sheets, $file = null, $uuid, $submissionData)
    {

        $this->userId = $userId;
        $this->sheetNames = $sheets;
        $this->file = $file;
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
        cache()->put($this->uuid . '_status', 'pending');
        cache()->put($this->uuid . '_progress', 0);
        cache()->put($this->uuid . '_errors', null);

        cache()->put("submissions.{$this->uuid}.main", []);
        cache()->put("submissions.{$this->uuid}.followup", []);
        cache()->put("submissions.{$this->uuid}.agreement", []);
        cache()->put("submissions.{$this->uuid}.market", []);
        cache()->put("submissions.{$this->uuid}.intermarket", []);

    }

    public function sheets(): array
    {
        return [
            new RpmFarmerImportSheet1($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmFarmerImportSheet2($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmFarmerImportSheet3($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmFarmerImportSheet4($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmFarmerImportSheet5($this->userId, $this->file, $this->uuid, $this->submissionData),
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

                    throw new UserErrorException("File contains invalid sheets!");

                }

                $sheets = $event->reader->getTotalRows();

                foreach ($sheets as $key => $sheet) {

                    if ($key == 'RTC_FARMERS' && $sheet <= 1) {

                        throw new UserErrorException("The first sheet can not contain empty rows!");


                    }
                }
            },








        ];
    }


}