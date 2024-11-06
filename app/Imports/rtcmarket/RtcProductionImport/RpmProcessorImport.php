<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use Exception;
use App\Models\User;
use App\Models\Submission;
use App\Models\ImportError;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorDomMarket;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use App\Models\RtcProductionProcessor;
use App\Notifications\JobNotification;
use App\Models\RpmProcessorInterMarket;
use App\Exceptions\SheetImportException;
use App\Models\RpmProcessorConcAgreement;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet1;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet2;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet3;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet4;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImportSheet5;

class RpmProcessorImport implements WithMultipleSheets, WithEvents, ShouldQueue, WithChunkReading, WithBatchInserts
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

            new RpmProcessorImportSheet1($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmProcessorImportSheet2($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmProcessorImportSheet3($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmProcessorImportSheet4($this->userId, $this->file, $this->uuid, $this->submissionData),
            new RpmProcessorImportSheet5($this->userId, $this->file, $this->uuid, $this->submissionData),
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
                    throw new UserErrorException("File contains invalid sheets!");

                }

                $sheets = $event->reader->getTotalRows();

                foreach ($sheets as $key => $sheet) {

                    if ($key == 'RTC_PROCESSORS') {
                        if ($sheet <= 1) {
                            throw new UserErrorException("The first sheet can not contain empty rows!");
                        }

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
                    $importJob->update([
                        'status' => 'failed',
                        'is_finished' => true
                    ]);
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


                    $processors = RtcProductionProcessor::where('uuid', $this->uuid)->pluck('id');


                    RpmProcessorFollowUp::whereIn('rpm_processor_id', $processors)->delete();
                    RpmProcessorInterMarket::whereIn('rpm_processor_id', $processors)->delete();
                    RpmProcessorConcAgreement::whereIn('rpm_processor_id', $processors)->delete();
                    RpmProcessorDomMarket::whereIn('rpm_processor_id', $processors)->delete();
                    Submission::where('batch_no', $uuid)->delete();
                    RtcProductionProcessor::where('uuid', $uuid)->delete();

                } else if ($exception instanceof UserErrorException) {
                    $failures = 'Something went wrong!';
                    Log::channel('system_log')->error('Import Error:' . $exception->getMessage());
                    $sheet = 'RTC_processorS';


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
                    $processors = RtcProductionProcessor::where('uuid', $this->uuid)->pluck('id');


                    RpmProcessorFollowUp::whereIn('rpm_processor_id', $processors)->delete();
                    RpmProcessorInterMarket::whereIn('rpm_processor_id', $processors)->delete();
                    RpmProcessorConcAgreement::whereIn('rpm_processor_id', $processors)->delete();
                    RpmProcessorDomMarket::whereIn('rpm_processor_id', $processors)->delete();
                    Submission::where('batch_no', $uuid)->delete();
                    RtcProductionProcessor::where('uuid', $uuid)->delete();
                    $user = User::find($this->userId);
                    $user->notify(new JobNotification($this->uuid, 'Unexpected error occured during import!', []));

                } else if ($exception instanceof Exception) {

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
                    $importJob->update([
                        'status' => 'completed',
                        'is_finished' => true
                    ]);
                }


                $user = User::find($this->userId);
                $user->notify(new JobNotification($this->uuid, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {
                    Submission::where('batch_no', $this->uuid)->update([
                        'status' => 'approved',
                    ]);
                    $processors = RtcProductionProcessor::where('uuid', $this->uuid)->pluck('id');
                    RtcProductionProcessor::where('uuid', $this->uuid)->update([
                        'status' => 'approved',
                    ]);
                    RpmProcessorFollowUp::whereIn('rpm_processor_id', $processors)->update([
                        'status' => 'approved',
                    ]);
                    RpmProcessorInterMarket::whereIn('rpm_processor_id', $processors)->update([
                        'status' => 'approved',
                    ]);
                    RpmProcessorConcAgreement::whereIn('rpm_processor_id', $processors)->update([
                        'status' => 'approved',
                    ]);
                    RpmProcessorDomMarket::whereIn('rpm_processor_id', $processors)->update([
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
