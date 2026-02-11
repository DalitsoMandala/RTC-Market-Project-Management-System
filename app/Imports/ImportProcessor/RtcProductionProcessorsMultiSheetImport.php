<?php

namespace App\Imports\ImportProcessor;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use App\Traits\FormEssentials;
use App\Helpers\ExcelValidator;
use App\Traits\ChecksBlankSheets;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Models\RtcProductionProcessor;
use App\Notifications\JobNotification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use App\Imports\ImportProcessor\RpmpMisImport;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
    use FormEssentials, ChecksBlankSheets;

    protected $expectedSheetNames = [
        'Production Processors',
        'Contractual Agreements',
        'Domestic Markets',
        'International Markets',
        'Market Information Systems',
        'Aggregation Centers'
    ];

    protected $expectedHeaders = [];

    protected $cacheKey;

    protected $filePath;

    protected $submissionDetails = [];

    protected $totalRows = 0;

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

    public function __construct($cacheKey, $filePath, $submissionDetails)
    {
        $this->cacheKey = $cacheKey;
        $this->filePath = $filePath;
        $this->submissionDetails = $submissionDetails;
        foreach ($this->expectedSheetNames as $sheetName) {
            $this->expectedHeaders[$sheetName] = array_keys($this->forms['Rtc Production Processors Form'][$sheetName]);
        }
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
                $rowCounts = $event->reader->getTotalRows();
                $this->assertBlankSheetRules(
                    rowCounts: $rowCounts,
                    required: [
                        'Production Processors' => 2,
                    ],
                    optional: [
                        'Contractual Agreements' => 2,
                        'Domestic Markets' => 2,
                        'International Markets' => 2,
                        'Market Information Systems' => 2,
                        'Aggregation Centers' => 2,
                    ],

                );
                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 2) ?? 0);  // exclude headers
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
                // $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
                    Submission::create([
                        'batch_no' => $this->cacheKey,
                        'form_id' => $this->submissionDetails['form_id'],
                        'period_id' => $this->submissionDetails['submission_period_id'],
                        'user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'batch_type' => 'batch',
                        'is_complete' => 1,
                        'table_name' => 'rtc_production_processors',
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
                        'table_name' => 'rtc_production_processors',
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
                        'table_name' => 'rtc_production_processors',
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

                RtcProductionProcessor::where('uuid', $this->cacheKey)->delete();
                Submission::where('batch_no', $this->cacheKey)->delete();

                Log::error($exception->getMessage());
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    // public function batchSize(): int
    // {
    //     return 1000;
    // }
}
