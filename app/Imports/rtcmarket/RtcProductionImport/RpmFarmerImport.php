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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;

class RpmFarmerImport implements WithMultipleSheets, WithEvents, ShouldQueue, WithChunkReading, WithBatchInserts
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


                $user = User::find($this->userId);
                $user->notify(new JobNotification($this->uuid, 'File import has started you will be notified when the file has finished importing!', []));

            },



            ImportFailed::class => function (ImportFailed $event) {
                $uuid = $this->uuid;
                $exception = $event->getException();
                $importJob = JobProgress::where('user_id', $this->userId)->where('job_id', $this->uuid)->where('is_finished', false)->first();
                if ($importJob) {
                    $importJob->update(['status' => 'failed', 'is_finished' => true]);
                }


                if ($exception instanceof SheetImportException) {
                    // Handle the custom exception
                    $failures = $exception->getErrors();
                    $sheet = $exception->getSheet();


                    $importErrors = ImportError::where('user_id', $this->userId)->where('uuid', $this->uuid)->first();
                    if ($importErrors) {

                        $importErrors->delete();
                    } else {
                        $getError = ImportError::create([
                            'uuid' => $uuid,
                            'errors' => json_encode($failures),
                            'sheet' => $sheet,
                            'type' => 'validation',
                            'user_id' => $this->userId,
                        ]);

                        $user = User::find($this->userId);
                        $user->notify(new JobNotification($this->uuid, 'Unexpected error occured during import, your file had validation errors!', json_decode($getError->errors), $sheet));

                    }



                    $farmers = RtcProductionFarmer::where('uuid', $this->uuid)->pluck('id');


                    RpmFarmerFollowUp::whereIn('rpm_farmer_id', $farmers)->delete();
                    RpmFarmerInterMarket::whereIn('rpm_farmer_id', $farmers)->delete();
                    RpmFarmerConcAgreement::whereIn('rpm_farmer_id', $farmers)->delete();
                    RpmFarmerDomMarket::whereIn('rpm_farmer_id', $farmers)->delete();
                    Submission::where('batch_no', $uuid)->delete();
                    RtcProductionFarmer::where('uuid', $uuid)->delete();

                } else if ($exception instanceof UserErrorException) {
                    $failures = 'Something went wrong!';
                    Log::channel('system_log')->error('Import Error:' . $exception->getMessage());
                    $sheet = 'RTC_FARMERS';


                    $importErrors = ImportError::where('user_id', $this->userId)->where('uuid', $this->uuid)->first();
                    if ($importErrors) {

                        $importErrors->delete();
                    } else {
                        ImportError::create([
                            'uuid' => $uuid,
                            'errors' => json_encode($failures),
                            'sheet' => $sheet,
                            'type' => 'normal',
                            'user_id' => $this->userId,
                        ]);
                    }
                    $farmers = RtcProductionFarmer::where('uuid', $this->uuid)->pluck('id');


                    RpmFarmerFollowUp::whereIn('rpm_farmer_id', $farmers)->delete();
                    RpmFarmerInterMarket::whereIn('rpm_farmer_id', $farmers)->delete();
                    RpmFarmerConcAgreement::whereIn('rpm_farmer_id', $farmers)->delete();
                    RpmFarmerDomMarket::whereIn('rpm_farmer_id', $farmers)->delete();
                    Submission::where('batch_no', $uuid)->delete();
                    RtcProductionFarmer::where('uuid', $uuid)->delete();

                    $user = User::find($this->userId);
                    $user->notify(new JobNotification($this->uuid, 'Unexpected error occured during import!', []));

                }


                Cache::put($this->uuid . '_status', 'finished');
                cache()->forget($this->uuid . '_progress');
                cache()->forget($this->uuid . '_total');

            },
            AfterImport::class => function (AfterImport $event) {
                $importJob = JobProgress::where('user_id', $this->userId)->where('job_id', $this->uuid)->first();
                if ($importJob) {
                    $importJob->update(['status' => 'completed', 'is_finished' => true]);
                }

                $user = User::find($this->userId);
                $user->notify(new JobNotification($this->uuid, 'Your file has finished importing, you can find your submissions on the submissions page!', []));


                if (($user->hasAnyRole('internal') && $user->hasAnyRole('organiser')) || $user->hasAnyRole('admin')) {
                    Submission::where('batch_no', $this->uuid)->update([
                        'status' => 'approved',
                    ]);
                    $farmers = RtcProductionFarmer::where('uuid', $this->uuid)->pluck('id');
                    RtcProductionFarmer::where('uuid', $this->uuid)->update([
                        'status' => 'approved',
                    ]);
                    RpmFarmerFollowUp::whereIn('rpm_farmer_id', $farmers)->update([
                        'status' => 'approved',
                    ]);
                    RpmFarmerInterMarket::whereIn('rpm_farmer_id', $farmers)->update([
                        'status' => 'approved',
                    ]);
                    RpmFarmerConcAgreement::whereIn('rpm_farmer_id', $farmers)->update([
                        'status' => 'approved',
                    ]);
                    RpmFarmerDomMarket::whereIn('rpm_farmer_id', $farmers)->update([
                        'status' => 'approved',
                    ]);
                }


                Cache::put($this->uuid . '_status', 'finished');
                cache()->forget($this->uuid . '_progress');
                cache()->forget($this->uuid . '_total');
            },





        ];
    }
    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }

}