<?php

namespace App\Imports\RtcRecruitment;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use App\Imports\ImportFarmer\RpmfMisImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Imports\ImportFarmer\RpmfBasicSeedImport;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\ImportFarmer\RpmfCertifiedSeedImport;
use App\Imports\ImportFarmer\RpmfAreaCultivationImport;
use App\Imports\ImportFarmer\RpmFarmerDomMarketsImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use App\Imports\ImportFarmer\RtcProductionFarmersImport;
use App\Imports\ImportFarmer\RpmFarmerInterMarketsImport;
use App\Imports\ImportFarmer\RpmfAggregationCentersImport;
use App\Imports\ImportFarmer\RpmFarmerConcAgreementsImport;
use App\Models\Recruitment;

class RtcRecruitmentMultiSheet implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    use Importable, RegistersEventListeners;

    protected $expectedSheetNames = [
        'RTC Actor Recruitment',

    ];
    protected $expectedHeaders = [

        'RTC Actor Recruitment' => [
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
            'Group',
            'Approach',
            'Sector',
            'Members Female 18-35',
            'Members Male 18-35',
            'Members Male 35+',
            'Members Female 35+',
            'Category',
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
            'Area Under Cultivation',
            'Is Registered Seed Producer',
            'Seed Producer Registration Number',
            'Seed Producer Registration Date',
            'Uses Certified Seed',

        ],
        'Seed Services Unit' => [
            'Recruitment ID',
            'Registration Date',
            'Registration Number',
            'Variety',
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

    private function getSheetHeaders(Worksheet $sheet): array
    {
        $highestColumn = $sheet->getHighestColumn();
        $headerCells = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false);
        return $headerCells[0] ?? [];
    }

    private function validateHeaders(array $actualHeaders, array $expectedHeaders): bool
    {
        return array_values(array_map('trim', $actualHeaders)) === array_values(array_map('trim', $expectedHeaders));
    }
    public function sheets(): array
    {
        return [
            'RTC Actor Recruitment' => new RtcRecruitmentImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Seed Services Unit' => new SeedServicesUnitImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {


                $firstSheetName = $this->expectedSheetNames[0];  // Get first sheet from the expected list
                $reader = IOFactory::createReaderForFile($this->filePath);
                $spreadsheet = $reader->load($this->filePath);
                $sheetNames = $spreadsheet->getSheetNames();


                $workBook = $event->reader->getTotalRows();

                foreach ($workBook as $sheetName => $totalRows) {
                    // Check if the sheet is blank
                    if ($totalRows <= 2) {  // Adjust this if you want to consider 0 rows as blank, 3rd row is validation
                        if ($sheetName === $firstSheetName) {
                            // Log error if the first sheet is blank
                            Log::error("The sheet '{$firstSheetName}' is blank.");
                            throw new ExcelValidationException(
                                "The sheet '{$firstSheetName}' is blank. Please ensure it contains data before importing."
                            );
                        }
                    }
                }




                // Validate headers and missing sheet names
                foreach ($this->expectedHeaders as $sheetName => $expectedHeaders) {
                    // Check if sheet exists

                    if (!in_array($sheetName, $sheetNames)) {
                        throw new ExcelValidationException("Sheet '{$sheetName}' is missing in the uploaded file.");
                    }

                    // Get the sheet by name
                    $sheet = $spreadsheet->getSheetByName($sheetName);

                    // Validate headers
                    $actualHeaders = $this->getSheetHeaders($sheet);
                    if (!$this->validateHeaders($actualHeaders, $expectedHeaders)) {
                        throw new ExcelValidationException("Headers in sheet '{$sheetName}' do not match the expected format.");
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

                //  $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if ($user->hasAnyRole('manager')) {
                    Submission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['submission_period_id'],
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
                } else if ($user->hasAnyRole('admin')) {
                    Submission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['submission_period_id'],
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
                            route('admin-submissions', [
                                'batch' => $this->cacheKey,
                            ], true) . '#batch-submission'

                        )
                    );
                } else if ($user->hasAnyRole('staff')) {
                    Submission::create([
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['submission_period_id'],
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
                        'period_id' => $this->submissionDetails['submission_period_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'pending',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'rtc_production_farmers',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);

                    $user->notify(new ImportSuccessNotification(
                        $this->cacheKey,
                        route('external-submissions', [
                            'batch' => $this->cacheKey,
                        ], true) . '#batch-submission'


                    ));
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

                if ($exception instanceof \App\Exceptions\UserErrorException) {
                    $errorMessage = $exception->getMessage();
                } else {
                    $errorMessage = "Something went wrong. Please try again.";
                }

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

                Recruitment::where('uuid', $this->cacheKey)->delete();
                Submission::where('batch_no', $this->cacheKey)->delete();


                // throw new ExcelValidationException($exception->getMessage());
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }
}