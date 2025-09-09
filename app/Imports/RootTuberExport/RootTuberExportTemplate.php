<?php

namespace App\Imports\RootTuberExport;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use App\Models\RtcConsumption;
use App\Traits\FormEssentials;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Imports\RootTuberExport\RawExportSheet;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\RootTuberExport\ProcessedExportSheet;
use App\Models\ProcessedTuberExport;
use App\Models\RawTuberExport;
use App\Models\RootTuberSubmission;

class RootTuberExportTemplate implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue, WithBatchInserts
{
    use FormEssentials;
    protected $expectedSheetNames = [
        'ROOTS & TUBER RAW EXPORTS',
        'ROOTS & TUBER PROCESSED EXPORTS'
    ];
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
            $this->expectedHeaders[$sheetName] = array_keys($this->forms['Root and Tuber Exports Form'][$sheetName]);
        }
    }

    public function sheets(): array
    {
        return [
            'ROOTS & TUBER RAW EXPORTS' => new RawExportSheet($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'ROOTS & TUBER PROCESSED EXPORTS' => new ProcessedExportSheet($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
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
                    //dd($actualHeaders, $expectedHeaders);
                    if (!$this->validateHeaders($actualHeaders, $expectedHeaders)) {
                        throw new ExcelValidationException("Headers in sheet '{$sheetName}' do not match the expected format.");
                    }
                }

                // Get total rows from all sheets and initialize JobProgress
                $rowCounts = $event->reader->getTotalRows();
                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 2) ?? 0); // excluding headers
                }, 0);

                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'total_rows' => $this->totalRows,
                        'processed_rows' => 0,
                        'progress' => 0,
                        'user_id' => $this->submissionDetails['user_id'],
                        'form_name' => 'Root Tuber Exports Form Import',
                    ]
                );

                Cache::put("{$this->cacheKey}_import_progress", 0, now()->addMinutes(30));
            },

            AfterImport::class => function (AfterImport $event) {
                // Finalize Submission record after import completes

                $user = User::find($this->submissionDetails['user_id']);
                //    $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
                if ($user->hasAnyRole('manager')) {
                    RootTuberSubmission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'submitted_user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'table_name' => '',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);

                    $user->notify(
                        new ImportSuccessNotification(
                            $this->cacheKey,
                            route('cip-submissions', [
                                'batch' => $this->cacheKey,
                            ], true) . '#market-submission'

                        )
                    );
                } else if ($user->hasAnyRole('admin')) {
                    RootTuberSubmission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'submitted_user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'table_name' => '',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);

                    $user->notify(
                        new ImportSuccessNotification(
                            $this->cacheKey,
                            route('admin-submissions', [
                                'batch' => $this->cacheKey,
                            ], true) . '#root-submission'

                        )
                    );
                } else if ($user->hasAnyRole('staff')) {
                    RootTuberSubmission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'submitted_user_id' => $this->submissionDetails['user_id'],
                        'status' => 'pending',
                        'table_name' => '',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);

                    $user->notify(new ImportSuccessNotification(
                        $this->cacheKey,
                        route('cip-staff-submissions', [
                            'batch' => $this->cacheKey,
                        ], true) . '#root-submission'

                    ));
                } else if ($user->hasAnyRole('external') && $user->organisation->name === 'RTCDT') {
                    RootTuberSubmission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'submitted_user_id' => $this->submissionDetails['user_id'],
                        'status' => 'approved',
                        'table_name' => '',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);
                    $user->notify(new ImportSuccessNotification(
                        $this->cacheKey,
                        route('external-submissions', [
                            'batch' => $this->cacheKey,
                        ], true) . '#root-submission'


                    ));
                } else {
                    RootTuberSubmission::create([
                        'batch_no' => $this->submissionDetails['batch_no'],
                        'submitted_user_id' => $this->submissionDetails['user_id'],
                        'status' => 'pending',
                        'table_name' => '',
                        'file_link' => $this->submissionDetails['file_link']
                    ]);
                    $user->notify(new ImportSuccessNotification(
                        $this->cacheKey,
                        route('external-submissions', [
                            'batch' => $this->cacheKey,
                        ], true) . '#root-submission'


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


                RawTuberExport::where('uuid', $this->cacheKey)->delete();
                ProcessedTuberExport::where('uuid', $this->cacheKey)->delete();
                RootTuberSubmission::where('batch_no', $this->cacheKey)->delete();

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
