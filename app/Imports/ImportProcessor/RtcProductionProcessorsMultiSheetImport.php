<?php

namespace App\Imports\ImportProcessor;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exceptions\ExcelValidationException;
use App\Imports\ImportProcessor\RtcProductionProcessorsImport;
use App\Imports\ImportProcessor\RpmProcessorConcAgreementsImport;
use App\Imports\ImportProcessor\RpmProcessorDomMarketsImport;
use App\Imports\ImportProcessor\RpmProcessorInterMarketsImport;
use App\Imports\ImportProcessor\RpmpMisImport;
use App\Imports\ImportProcessor\RpmpAggregationCentersImport;

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
                        throw new ExcelValidationException("The sheet '{$expectedSheetName}' is missing.");
                    }
                }

                foreach ($sheetNames as $sheetName) {
                    if (!in_array($sheetName, $this->expectedSheetNames)) {
                        Log::error("Unexpected sheet name: {$sheetName}");
                        throw new ExcelValidationException("Unexpected sheet: '{$sheetName}' in file.");
                    }
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
                if (($user->hasAnyRole('internal') && $user->hasAnyRole('organiser')) || $user->hasAnyRole('admin')) {
                    Submission::create([
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['period_month_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'rtc_production_farmers',
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
                        'table_name' => 'rtc_production_farmers',
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
