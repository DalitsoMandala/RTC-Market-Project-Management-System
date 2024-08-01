<?php

namespace App\Imports\rtcmarket\HouseholdImport;

use App\Exceptions\ImportDataBaseError;
use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ArrayToUpperCase;
use App\Helpers\ImportValidateHeading;
use App\Jobs\chuckReader;
use App\Jobs\ProcessDataChunk;
use App\Jobs\SaveSubmissionData;
use App\Jobs\SaveTableData;
use App\Models\HouseholdRtcConsumption;
use App\Models\ImportError;
use App\Models\JobProgress;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\JobNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Ramsey\Uuid\Uuid;

HeadingRowFormatter::default('none');

class HrcImport implements ToCollection, WithHeadingRow, WithEvents, WithValidation, SkipsOnFailure, WithChunkReading, ShouldQueue
{
    /**
     * @param Collection $collection
     */
    use Importable, RegistersEventListeners;
    private $failures = [];

    protected $errors = [];
    protected $expectedHeadings = [
        'DATE OF ASSESSMENT',
        'ACTOR TYPE',
        'RTC GROUP PLATFORM',
        'PRODUCER ORGANISATION',
        'ACTOR NAME',
        'AGE GROUP',
        'SEX',
        'PHONE NUMBER',
        'HOUSEHOLD SIZE',
        'UNDER 5 IN HOUSEHOLD',
        'RTC CONSUMERS',
        'RTC CONSUMERS/POTATO',
        'RTC CONSUMERS/SWEET POTATO',
        'RTC CONSUMERS/CASSAVA',
        'RTC CONSUMPTION FREQUENCY',
        'RTC MAIN FOOD/CASSAVA',
        'RTC MAIN FOOD/POTATO',
        'RTC MAIN FOOD/SWEET POTATO',
    ];
    public $sheetNames = [];
    public $expectedSheetNames = ['HH_CONSUMPTION'];
    public $location, $userId, $file;
    public $uuid;

    public $totalRows;
    public $submissionData = [];
    public function __construct($userId, $sheets, $file = null, $uuid, $submissionData)
    {
        $this->userId = $userId;
        $this->sheetNames = $sheets;
        $this->file = $file;
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
        cache()->put($this->uuid . '_status', 'pending', now()->addMinutes(10));
        cache()->put($this->uuid . '_progress', 0, now()->addMinutes(10));
        cache()->put($this->uuid . '_errors', null, now()->addMinutes(10));
        cache()->put("submissions.{$this->uuid}.main", []);

    }


    public function collection(Collection $collection)
    {

        if (!empty($this->failures)) {

            throw new SheetImportException('HH_CONSUMPTION', $this->failures);
        }

        //start processing the job
        $importJob = JobProgress::where('user_id', $this->userId)->where('job_id', $this->uuid)->where('is_finished', false)->first();
        if ($importJob) {
            $importJob->update(['status' => 'processing']);
        }
        $submissionData = $this->submissionData;
        $uuid = $this->uuid;
        $batch = [];
        foreach ($collection as $row) {
            # code...



            $entry = [
                'submission_period_id' => $submissionData['submission_period_id'],
                'organisation_id' => $submissionData['organisation_id'],
                'financial_year_id' => $submissionData['financial_year_id'],
                'period_month_id' => $submissionData['period_month_id'],
                'location_data' => json_encode([
                    'epa' => $row['EPA'],
                    'district' => $row['DISTRICT'],
                    'section' => $row['SECTION'],
                    'enterprise' => $row['ENTERPRISE'],
                ]),
                'date_of_assessment' => $row['DATE OF ASSESSMENT'],
                'actor_type' => $row['ACTOR TYPE'],
                'rtc_group_platform' => $row['RTC GROUP PLATFORM'],
                'producer_organisation' => $row['PRODUCER ORGANISATION'],
                'actor_name' => $row['ACTOR NAME'],
                'age_group' => $row['AGE GROUP'],
                'sex' => $row['SEX'],
                'phone_number' => $row['PHONE NUMBER'],
                'household_size' => $row['HOUSEHOLD SIZE'],
                'under_5_in_household' => $row['UNDER 5 IN HOUSEHOLD'],
                'rtc_consumers' => $row['RTC CONSUMERS'],
                'rtc_consumers_potato' => $row['RTC CONSUMERS/POTATO'],
                'rtc_consumers_sw_potato' => $row['RTC CONSUMERS/SWEET POTATO'],
                'rtc_consumers_cassava' => $row['RTC CONSUMERS/CASSAVA'],
                'rtc_consumption_frequency' => $row['RTC CONSUMPTION FREQUENCY'],
                'user_id' => $this->userId,
                'uuid' => $uuid,
                'main_food_data' => [],
            ];

            if ($row['RTC MAIN FOOD/CASSAVA'] === 'YES') {
                $entry['main_food_data'][] = ['name' => 'CASSAVA'];
            }
            if ($row['RTC MAIN FOOD/POTATO'] === 'YES') {
                $entry['main_food_data'][] = ['name' => 'POTATO'];
            }
            if ($row['RTC MAIN FOOD/SWEET POTATO'] === 'YES') {
                $entry['main_food_data'][] = ['name' => 'SWEET POTATO'];
            }
            $entry['main_food_data'] = json_encode($entry['main_food_data']);

            $batch[] = $entry;
        }

        $this->processBatch($batch, $submissionData, $uuid, $importJob);
    }


    protected function processBatch($batch, $submissionData, $uuid, $importJob)
    {

        $existingData = cache()->get("submissions.{$this->uuid}.main", []);
        $mergedData = array_merge($existingData, $batch);
        cache()->put("submissions.{$this->uuid}.main", $mergedData);

        foreach ($batch as $dt) {
            HouseholdRtcConsumption::create($dt);
        }

        // Update progress
        $progress = count($mergedData);
        $percentage = floor(($progress / $this->totalRows)) * 100;
        cache()->put($this->uuid . '_progress', $percentage, now()->addMinutes(10));

        if ($importJob) {
            $importJob->update(['progress' => $percentage]);
        }

        // Create submission only when progress reaches 100%
        if ($percentage >= 100) {
            unset($submissionData['submission_period_id']);
            unset($submissionData['organisation_id']);
            unset($submissionData['financial_year_id']);
            unset($submissionData['period_month_id']);
            $submissionData['batch_no'] = $uuid;
            $submissionData['data'] = json_encode($mergedData);

            Submission::create($submissionData);
        }
    }



    public function chunkSize(): int
    {
        return 200;
    }
    public function onFailure(Failure ...$failures)
    {

        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }





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

                    if ($key == 'HH_CONSUMPTION' && $sheet <= 1) {
                        throw new UserErrorException("The first sheet cannot contain empty rows!");
                    }
                }

                $headings = (new HeadingRowImport)->toArray($this->file);
                $headings = $headings[0][0];

                // Check if the headings match the expected headings
                $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);
                if (count($missingHeadings) > 0) {
                    throw new UserErrorException("This file has invalid headings. Please upload your data using the template file above");
                }

                $this->totalRows = intval($sheets['HH_CONSUMPTION']) - 1;


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
                    $sheet = 'HH_CONSUMPTION';


                    $importErrors = ImportError::where('user_id', $this->userId)->where('uuid', $this->uuid)->first();
                    if ($importErrors) {

                        $importErrors->delete();
                    } else {
                        ImportError::create([
                            'uuid' => $uuid,
                            'errors' => json_encode($failures),
                            'sheet' => $sheet,
                            'type' => 'validation',
                            'user_id' => $this->userId,
                        ]);
                    }

                    $user = User::find($this->userId);
                    $user->notify(new JobNotification($this->uuid, 'Unexpected error occured during import!'));


                    Submission::where('batch_no', $uuid)->delete();
                    HouseholdRtcConsumption::where('uuid', $uuid)->delete();


                } else if ($exception instanceof UserErrorException) {
                    $failures = 'Something went wrong!';
                    \Log::channel('system_log')->error('Import Error:' . $exception->getMessage());
                    $sheet = 'HH_CONSUMPTION';


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

                    Submission::where('batch_no', $uuid)->delete();
                    HouseholdRtcConsumption::where('uuid', $uuid)->delete();


                }

                $user = User::find($this->userId);
                $user->notify(new JobNotification($this->uuid, 'Unexpected error occured during import, your file had validation errors!'));

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
                $user->notify(new JobNotification($this->uuid, 'Your file has finished importing, you can find your submissions on the submissions page!'));
                if ($user->hasAnyRole('organiser') || $user->hasAnyRole('admin')) {
                    HouseholdRtcConsumption::where('uuid', $this->uuid)->update([
                        'status' => 'approved',
                    ]);
                }
                Cache::put($this->uuid . '_status', 'finished');
                cache()->forget($this->uuid . '_progress');
                cache()->forget($this->uuid . '_total');
            },

        ];
    }
    public function rules(): array
    {
        return [
            '*.EPA' => 'string|max:255|nullable',
            '*.DISTRICT' => 'string|max:255|nullable',
            '*.SECTION' => 'string|max:255|nullable',
            '*.ENTERPRISE' => 'string|max:255|nullable',
            '*.DATE OF ASSESSMENT' => 'date|nullable',
            '*.ACTOR TYPE' => 'string|max:255|in:FARMER,PROCESSOR,TRADER,INDIVIDUALS FROM NUTRITION INTERVENTION,OTHER|nullable',
            '*.RTC GROUP PLATFORM' => 'string|max:255|nullable',
            '*.PRODUCER ORGANISATION' => 'string|max:255|nullable',
            '*.ACTOR NAME' => 'string|max:255|nullable',
            '*.AGE GROUP' => 'string|max:255|in:YOUTH,NOT YOUTH|nullable',
            '*.SEX' => 'string|in:MALE,FEMALE|nullable',
            '*.PHONE NUMBER' => 'string|max:255|nullable',
            '*.HOUSEHOLD SIZE' => 'numeric|min:1|nullable',
            '*.UNDER 5 IN HOUSEHOLD' => 'integer|min:0|nullable',
            '*.RTC CONSUMERS' => 'numeric|min:0|nullable',
            '*.RTC CONSUMERS/POTATO' => 'numeric|min:0|nullable',
            '*.RTC CONSUMERS/SWEET POTATO' => 'integer|min:0|nullable',
            '*.RTC CONSUMERS/CASSAVA' => 'numeric|min:0|nullable',
            '*.RTC CONSUMPTION FREQUENCY' => 'numeric|max:255|nullable',
            '*.RTC MAIN FOOD/CASSAVA' => 'string|in:YES,NO|nullable',
            '*.RTC MAIN FOOD/POTATO' => 'string|in:YES,NO|nullable',
            '*.RTC MAIN FOOD/SWEET POTATO' => 'string|in:YES,NO|nullable',
        ];
    }



}