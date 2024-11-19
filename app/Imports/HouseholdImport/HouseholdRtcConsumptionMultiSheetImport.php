<?php

namespace App\Imports\HouseholdImport;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use App\Helpers\ExcelValidator;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\HouseholdImport\MainFoodSheetImport;
use App\Imports\HouseholdImport\HouseholdSheetImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class HouseholdRtcConsumptionMultiSheetImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    use Importable, RegistersEventListeners;
    protected $expectedSheetNames = [
        'Household Data',
        'Main Food Data'
    ];
    protected $expectedHeaders = [
        'Household Data' => [
            'ID',
            'EPA',
            'Section',
            'District',
            'Enterprise',
            'Date of Assessment',
            'Actor Type (Farmer, Trader, etc.)',
            'RTC Group/Platform',
            'Producer Organisation',
            'Actor Name',
            'Age Group',
            'Sex',
            'Phone Number',
            'Household Size',
            'Under 5 in Household',
            'RTC Consumers (Total)',
            'RTC Consumers - Potato',
            'RTC Consumers - Sweet Potato',
            'RTC Consumers - Cassava',
            'RTC Consumption Frequency',
        ],
        'Main Food Data' => [
            'Household ID',
            'Main Food Name',
        ]
    ];
    protected $cacheKey;
    protected $filePath;

    protected $submissionDetails = [];

    protected $totalRows = 0;
    public function __construct($cacheKey, $filePath, $submissionDetails)
    {
        $this->cacheKey = $cacheKey;
        $this->filePath = $filePath;
        $this->submissionDetails = $submissionDetails;
    }
    public function sheets(): array
    {
        return [
            'Household Data' => new HouseholdSheetImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Main Food Data' => new MainFoodSheetImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }

    public function registerEvents(): array
    {
        return [
                // Handle by a closure.
            BeforeImport::class => function (BeforeImport $event) {
                $sheetNames = SheetNamesValidator::getSheetNames($this->filePath);

                // Check if all expected sheets are present
                foreach ($this->expectedSheetNames as $expectedSheetName) {
                    if (!in_array($expectedSheetName, $sheetNames)) {
                        Log::error("Missing expected sheet: {$expectedSheetName}");
                        throw new ExcelValidationException(
                            "The sheet '{$expectedSheetName}' is missing. Please ensure the file contains all required sheets."
                        );
                    }
                }

                // Check for any unexpected sheets
                foreach ($sheetNames as $sheetName) {
                    if (!in_array($sheetName, $this->expectedSheetNames)) {
                        Log::error("Unexpected sheet name: {$sheetName}");

                        throw new ExcelValidationException(
                            "Unexpected sheet: '{$sheetName}' in file."
                        );
                    }
                }

                $filePath = $this->filePath;
                $expectedSheetNames = $this->expectedSheetNames;
                $expectedHeaders = $this->expectedHeaders;

                $validator = new ExcelValidator($filePath, $expectedSheetNames, $expectedHeaders);
                $message = $validator->validateHeaders();

                if ($message) {
                    throw new ExcelValidationException($message->getMessage());
                }

                // Get total rows from both sheets
                $rowCounts = $event->reader->getTotalRows();
                $this->totalRows = (($rowCounts['Household Data'] - 1) ?? 0) + (($rowCounts['Main Food Data'] - 1) ?? 0); // others are header rows
                // Initialize JobProgress record
    

                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'total_rows' => $this->totalRows,
                        'processed_rows' => 0,
                        'progress' => 0,
                        'user_id' => $this->submissionDetails['user_id'],
                        'form_name' => 'Household RTC Consumption',
                    ]
                );

                // Initialize progress cache
                Cache::put("{$this->cacheKey}_import_progress", 0, now()->addMinutes(30));
            },
            AfterImport::class => function (AfterImport $event) {
                $user = User::find($this->submissionDetails['user_id']);
                $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {
                    Submission::create([
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['period_month_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'household_rtc_consumption',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);
                } else {
                    Submission::create([
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['period_month_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'pending',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'household_rtc_consumption',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);
                }

                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'status' => 'completed',
                        'progress' => 100,
                    ]
                );
            },

            ImportFailed::class => function (ImportFailed $event) {

                $exception = $event->getException();

                $errorMessage = $exception->getMessage();

                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'status' => 'failed',
                        'progress' => 100,
                        'error' => $errorMessage,
                    ]
                );

                Log::error($exception->getMessage());
            }




        ];
    }



    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }
}
