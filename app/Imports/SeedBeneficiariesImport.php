<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use App\Helpers\ExcelValidator;
use App\Models\SeedBeneficiary;
use App\Imports\CropSheetImport;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\ValidationException;

class SeedBeneficiariesImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    protected $expectedSheetNames = [
        'Potato',
        'OFSP',
        'Cassava'
        //  'Mother Plot Hosts',
        // 'Cassava Tots'
    ];

    protected $expectedHeaders = [
        'Potato' => [
            //   'Crop',
            'District',
            'EPA',
            'Section',
            'Name of AEDO',
            'AEDO Phone Number',
            'Date of Distribution',
            'Name of Recipient',
            'Group Name',
            'Village',
            'Sex',
            'Age',
            'Marital Status',
            'Household Head',
            'Household Size',
            'Children Under 5 in HH',
            'Variety Received',
            'Amount Of Seed Received',
            'National ID',
            'Phone Number',
            'Signed',
            'Year',
            'Season Type'
        ],
        'OFSP' => [
            //   'Crop',
            'District',
            'EPA',
            'Section',
            'Name of AEDO',
            'AEDO Phone Number',
            'Date of Distribution',
            'Name of Recipient',
            'Group Name',
            'Village',
            'Sex',
            'Age',
            'Marital Status',
            'Household Head',
            'Household Size',
            'Children Under 5 in HH',
            'Variety Received',
            'Bundles Received',
            'National ID',
            'Phone Number',
            'Signed',
            'Year',
            'Season Type'
        ],
        'Cassava' => [
            //   'Crop',
            'District',
            'EPA',
            'Section',
            'Name of AEDO',
            'AEDO Phone Number',
            'Date of Distribution',
            'Name of Recipient',
            'Group Name',
            'Village',
            'Sex',
            'Age',
            'Marital Status',
            'Household Head',
            'Household Size',
            'Children Under 5 in HH',
            'Variety Received',
            'Amount Received',
            'National ID',
            'Phone Number',
            'Signed',
            'Year',
            'Season Type'
        ],


        // 'Mother Plot Hosts' => [
        //     'District',
        //     'EPA',
        //     'Section',
        //     'Village',
        //     'GPS S',
        //     'GPS E',
        //     'Elevation',
        //     'Season',
        //     'Date of Planting',
        //     'Name of Farmer',
        //     'Sex',
        //     'Nat ID / Phone #',
        //     'Variety Received'
        // ],
        // 'Cassava Tots' => [
        //     'Name',
        //     'Gender',
        //     'Age Group',
        //     'District',
        //     'EPA',
        //     'Position',
        //     'Phone Number',
        //     'Email Address'
        // ]
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
            'Potato' => new CropSheetImport('Potato', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            'OFSP' => new CropSheetImportOFSP('OFSP', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Cassava' => new CropSheetImportCassava('Cassava', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            //  'Cassava' => new CropSheetImport('Cassava', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            //  'Trainings' => new TrainingsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            //    'Mother Plot Hosts' => new MotherPlotImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            //  'Cassava Tots' => new CassavaTotImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
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



                // Check if the first sheet is blank
                $sheetNames = $this->expectedSheetNames;
                $sheets = $event->reader->getTotalRows();

                $countBlanks = 0;

                foreach ($sheets as $key => $sheet) {
                    if (
                        ($key == $sheetNames[0] && $sheet <= 1) ||
                        ($key == $sheetNames[1] && $sheet <= 2) ||
                        ($key == $sheetNames[2] && $sheet <= 3)
                    ) {
                        $countBlanks++;
                    }
                }

                if ($countBlanks == 3) {
                    Log::error("The sheets are all blank.");
                    throw new ExcelValidationException(
                        "The sheets are all blank. Please ensure your file contains data before importing."
                    );
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
                        'form_name' => 'Seed Beneficiaries Import',
                    ]
                );

                Cache::put("{$this->cacheKey}_import_progress", 0, now()->addMinutes(30));
            },

            AfterImport::class => function (AfterImport $event) {
                $user = User::find($this->submissionDetails['user_id']);
                $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
                    Submission::create([
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['submission_period_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'seed_beneficiaries',
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
                        'period_id' => $this->submissionDetails['submission_period_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'seed_beneficiaries',
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
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['submission_period_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'pending',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'seed_beneficiaries',
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
                        'progress' => 100
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

                SeedBeneficiary::where('uuid', $this->cacheKey)->delete();

                Log::error($exception->getMessage());
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
