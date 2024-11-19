<?php

namespace App\Imports\ImportProcessor;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use App\Helpers\ExcelValidator;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use App\Imports\ImportProcessor\RpmpMisImport;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\ImportProcessor\RpmpAggregationCentersImport;
use App\Imports\ImportProcessor\RpmProcessorDomMarketsImport;
use App\Imports\ImportProcessor\RtcProductionProcessorsImport;
use App\Imports\ImportProcessor\RpmProcessorInterMarketsImport;
use App\Imports\ImportProcessor\RpmProcessorConcAgreementsImport;

class RtcProductionProcessorsMultiSheetImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    use Importable;

    protected $expectedSheetNames = [
        'Production Processors',
        'Contractual Agreements',
        'Domestic Markets',
        'International Markets',
        'Market Information Systems',
        'Aggregation Centers'
    ];

    protected $expectedHeaders = [
        'Production Processors' => [
            'ID',
            'EPA',
            'Section',
            'District',
            'Enterprise',
            'Date of Recruitment',
            'Name of Actor',
            'Name of Representative',
            'Phone Number',
            'Type',
            'Approach',
            'Sector',
            'Members Female 18-35',
            'Members Male 18-35',
            'Members Male 35+',
            'Members Female 35+',
            'Group',
            'Establishment Status',
            'Is Registered',
            'Registration Body',
            'Registration Number',
            'Registration Date',
            'Employees Formal Female 18-35',
            'Employees Formal Male 18-35',
            'Employees Formal Male 35+',
            'Employees Formal Female 35+',
            'Employees Informal Female 18-35',
            'Employees Informal Male 18-35',
            'Employees Informal Male 35+',
            'Employees Informal Female 35+',
            'Market Segment Fresh',
            'Market Segment Processed',
            'Has RTC Market Contract',
            'Total Volume Production Previous Season',
            'Production Value Previous Season Total',
            'Date of Max Sales',
            'USD Rate',
            'USD Value',
            'Sells to Domestic Markets',
            'Sells to International Markets',
            'Uses Market Info Systems',
            'Sells to Aggregation Centers',
            'Total Volume Aggregation Center Sales'
        ],
        'Contractual Agreements' => [
            'Processor ID',
            'Date Recorded',
            'Partner Name',
            'Country',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ],
        'Domestic Markets' => [
            'Processor ID',
            'Date Recorded',
            'Crop Type',
            'Market Name',
            'District',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ],
        'International Markets' => [
            'Processor ID',
            'Date Recorded',
            'Crop Type',
            'Market Name',
            'Country',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ],
        'Market Information Systems' => [
            'MIS Name',
            'Processor ID'
        ],
        'Aggregation Centers' => [
            'Aggregation Center Name',
            'Processor ID'
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
            'Production Processors' => new RtcProductionProcessorsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Contractual Agreements' => new RpmProcessorConcAgreementsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Domestic Markets' => new RpmProcessorDomMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'International Markets' => new RpmProcessorInterMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Market Information Systems' => new RpmpMisImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Aggregation Centers' => new RpmpAggregationCentersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $sheetNames = SheetNamesValidator::getSheetNames($this->filePath);

                // Validate expected and unexpected sheet names
                foreach ($this->expectedSheetNames as $expectedSheetName) {
                    if (!in_array($expectedSheetName, $sheetNames)) {
                        Log::error("Missing expected sheet: {$expectedSheetName}");
                        throw new ExcelValidationException("The sheet '{$expectedSheetName}' is missing. Please ensure the file contains all required sheets.");
                    }
                }

                foreach ($sheetNames as $sheetName) {
                    if (!in_array($sheetName, $this->expectedSheetNames)) {
                        Log::error("Unexpected sheet name: {$sheetName}");
                        throw new ExcelValidationException("Unexpected sheet: '{$sheetName}' in file.");
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

                // Get total rows from all sheets and initialize JobProgress
                $rowCounts = $event->reader->getTotalRows();
                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 1) ?? 0); // excluding headers
                }, 0);

                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'total_rows' => $this->totalRows,
                        'processed_rows' => 0,
                        'progress' => 0,
                        'user_id' => $this->submissionDetails['user_id'],
                        'form_name' => 'Production Processors Import',
                    ]
                );

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
                        'table_name' => 'rtc_production_processors',
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
                        'table_name' => 'rtc_production_processors',
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
        return 1000;
    }
}
