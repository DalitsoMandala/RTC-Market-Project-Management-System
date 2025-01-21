<?php

namespace App\Imports\ImportFarmer;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use App\Helpers\ExcelValidator;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerBasicSeed;
use App\Models\RpmFarmerDomMarket;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\RpmFarmerInterMarket;
use Illuminate\Support\Facades\Cache;
use App\Models\RpmFarmerCertifiedSeed;
use App\Models\RpmFarmerConcAgreement;
use App\Notifications\JobNotification;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;
use App\Models\RpmFarmerAggregationCenter;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use App\Imports\ImportFarmer\RpmfMisImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
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
    protected $expectedHeaders = [

        'Production Farmers' => [
            'ID', // Add ID as the first column heading
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
            'Number of Plantlets Produced Cassava',
            'Number of Plantlets Produced Potato',
            'Number of Plantlets Produced Sweet Potato',
            'Screen House Vines Harvested',
            'Screen House Min Tubers Harvested',
            'SAH Plants Produced',
            'Is Registered Seed Producer',
            'Seed Producer Registration Number',
            'Seed Producer Registration Date',
            'Uses Certified Seed',
            'Market Segment Fresh',
            'Market Segment Processed',
            'Has RTC Market Contract',
            'Total Volume Production Previous Season',
            'Production Value Previous Season Total',
            'Production Value Date of Max Sales',
            'Production Value USD Rate',
            'Production Value USD Value',
            'Total Volume Irrigation Production Previous Season',
            'Irrigation Production Value Total',
            'Irrigation Production Date of Max Sales',
            'Irrigation Production USD Rate',
            'Irrigation Production USD Value',
            'Sells to Domestic Markets',
            'Sells to International Markets',
            'Uses Market Information Systems',
            'Sells to Aggregation Centers',
            'Total Volume Aggregation Center Sales'
        ],
        'Contractual Agreements' => [
            'Farmer ID',
            'Date Recorded',
            'Partner Name',
            'Country',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ],
        'Domestic Markets' => [
            'Farmer ID',
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
            'Farmer ID',
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
            'Name',
            'Farmer ID'
        ],
        'Aggregation Centers' => [
            'Name',
            'Farmer ID'
        ],
        'Basic Seed' => [
            'Variety',
            'Area',
            'Farmer ID'
        ],
        'Certified Seed' => [
            'Variety',
            'Area',
            'Farmer ID'
        ],
        'Area Cultivation' => [
            'Variety',
            'Area',
            'Farmer ID'
        ],

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
                            "Unexpected sheet: '{$sheetName}' in file."
                        );
                    }
                }


                // Check if the first sheet is blank
                $firstSheetName = $this->expectedSheetNames[0];
                $sheets = $event->reader->getTotalRows();

                foreach ($sheets as $key => $sheet) {

                    if ($sheet <= 1 && $firstSheetName) {

                        Log::error("The sheet '{$firstSheetName}' is blank.");
                        throw new ExcelValidationException(
                            "The sheet '{$firstSheetName}' is blank. Please ensure it contains data before importing."
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
                if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
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

                    $user->notify(
                        new ImportSuccessNotification(
                            $this->cacheKey,
                            route('cip-submissions', [
                                'batch' => $this->cacheKey,
                            ], true) . '#batch-submission'

                        )
                    );
                } else if ($user->hasAnyRole('staff')) {
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

                    $user->notify(new ImportSuccessNotification(
                        $this->cacheKey,
                        route('cip-staff-submissions', [
                            'batch' => $this->cacheKey,
                        ], true) . '#batch-submission'

                    ));
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
                $user->notify(new ImportSuccessNotification(
                    $this->cacheKey,
                    route('external-submissions', [
                        'batch' => $this->cacheKey,
                    ], true) . '#batch-submission'


                ));



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

                $user = User::find($this->submissionDetails['user_id']);
                $user->notify(new ImportFailureNotification(
                    $errorMessage,
                    $this->submissionDetails['route'],
                    $this->cacheKey,

                ));
                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'status' => 'failed',
                        'error' => $errorMessage,
                    ]
                );

                Log::error($exception->getMessage());

                RtcProductionFarmer::where('uuid', $this->cacheKey)->delete();
                Submission::where('uuid', $this->cacheKey)->delete();


                // throw new ExcelValidationException($exception->getMessage());
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }
}
