<?php

namespace App\Imports;

use App\Exceptions\ExcelValidationException;
use App\Helpers\ExcelValidator;
use App\Helpers\SheetNamesValidator;
use App\Imports\CropSheetImport;
use App\Models\JobProgress;
use App\Models\SeedBeneficiary;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use App\Notifications\JobNotification;
use App\Traits\ChecksBlankSheets;
use App\Traits\FormEssentials;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Validators\ValidationException;

class SeedBeneficiariesImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    protected $expectedSheetNames = [
        'Potato',
        'OFSP',
        'Cassava'
    ];

    use FormEssentials, ChecksBlankSheets;

    protected $expectedHeaders = [];

    protected $cacheKey;

    protected $filePath;

    protected $submissionDetails = [];

    protected $totalRows = 0;

    public function __construct($cacheKey, $filePath, $submissionDetails)
    {

        $this->cacheKey = $cacheKey;
        $this->filePath = $filePath;
        $this->submissionDetails = $submissionDetails;
        foreach ($this->expectedSheetNames as $sheetName) {
            $this->expectedHeaders[$sheetName] = array_keys($this->forms['Seed Beneficiaries Form'][$sheetName]);
        }
    }

    public function sheets(): array
    {
        return [
            'Potato' => new CropSheetImport('Potato', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            'OFSP' => new CropSheetImportOFSP('OFSP', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Cassava' => new CropSheetImportCassava('Cassava', $this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $rowCounts = $event->reader->getTotalRows();

                $this->assertBlankSheetRules(
                    rowCounts: $rowCounts,
                    required: [

                    ],
                    optional: [
                        'Potato' => 2, // 2 header rows,
                        'OFSP' => 2,
                        'Cassava' => 2
                    ],

                );
                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 2) ?? 0);  // excluding headers
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
                        'file_link' => $this->submissionDetails['file_link'],
                        'description' => $this->submissionDetails['description']
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
                        'file_link' => $this->submissionDetails['file_link'],
                        'description' => $this->submissionDetails['description']
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
                        'file_link' => $this->submissionDetails['file_link'],
                        'description' => $this->submissionDetails['description']
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
