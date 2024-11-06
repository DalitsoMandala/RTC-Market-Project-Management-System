<?php

namespace App\Imports\ImportFarmer;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use App\Imports\ImportFarmer\RpmfMisImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Imports\ImportFarmer\RpmfBasicSeedImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\ImportFarmer\RpmfCertifiedSeedImport;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\ImportFarmer\RpmfAreaCultivationImport;
use App\Imports\ImportFarmer\RpmFarmerDomMarketsImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use App\Imports\ImportFarmer\RtcProductionFarmersImport;
use App\Imports\ImportFarmer\RpmFarmerInterMarketsImport;
use App\Imports\ImportFarmer\RpmfAggregationCentersImport;
use App\Imports\ImportFarmer\RpmFarmerConcAgreementsImport;

class RtcProductionFarmersMultiSheetImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    use Importable, RegistersEventListeners;

    protected $expectedSheetNames = [
        'Production Farmers',
        'Contractual Agreements',
        'Domestic Markets',
        'International Markets',
        'Market Information Systems',
        'Aggregation Centers',
        'Basic Seed',
        'Certified Seed',
        'Area Cultivation'
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
            'Production Farmers' => new RtcProductionFarmersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Contractual Agreements' => new RpmFarmerConcAgreementsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Domestic Markets' => new RpmFarmerDomMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'International Markets' => new RpmFarmerInterMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Market Information Systems' => new RpmfMisImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Aggregation Centers' => new RpmfAggregationCentersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Basic Seed' => new RpmfBasicSeedImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Certified Seed' => new RpmfCertifiedSeedImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Area Cultivation' => new RpmfAreaCultivationImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $sheetNames = SheetNamesValidator::getSheetNames($this->filePath);

                // Validate sheet names
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
                            "The sheet '{$sheetName}' is not recognized. Please ensure the file contains only the required sheets."
                        );
                    }
                }

                // Get total rows from all sheets
                $rowCounts = $event->reader->getTotalRows();
                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 1) ?? 0); // exclude headers
                }, 0);

                // Initialize JobProgress record
                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'total_rows' => $this->totalRows,
                        'processed_rows' => 0,
                        'progress' => 0,
                        'user_id' => $this->submissionDetails['user_id'],
                        'form_name' => 'Production Farmers Import',
                    ]
                );

                Cache::put("{$this->cacheKey}_import_progress", 0, now()->addMinutes(30));
            },

            AfterImport::class => function (AfterImport $event) {
                // Finalize Submission record after import completes
    
                $user = User::find($this->submissionDetails['user_id']);
                $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {
                    Submission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
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
                        'batch_no' => $this->submissionDetails['batch_no'],
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

                // throw new ExcelValidationException($exception->getMessage());
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }
}
