<?php

namespace App\Imports\AttendanceImport;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\AttendanceImport\AttendanceRegistersImport;

class AttendanceRegistersMultiSheetImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue
{
    protected $expectedSheetNames = [
        'Attendance Registers',

    ];
    protected $cacheKey;
    protected $filePath;
    protected $submissionDetails;
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
            'Attendance Registers' => new AttendanceRegistersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),

        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $sheetNames = SheetNamesValidator::getSheetNames($this->filePath);

                // Validate expected sheet names
                foreach ($this->expectedSheetNames as $expectedSheetName) {
                    if (!in_array($expectedSheetName, $sheetNames)) {
                        Log::error("Missing expected sheet: {$expectedSheetName}");
                        throw new ExcelValidationException("The sheet '{$expectedSheetName}' is missing.");
                    }
                }

                // Check for unexpected sheets
                foreach ($sheetNames as $sheetName) {
                    if (!in_array($sheetName, $this->expectedSheetNames)) {
                        Log::error("Unexpected sheet name: {$sheetName}");
                        throw new ExcelValidationException("Unexpected sheet: '{$sheetName}' in file.");
                    }
                }


                // $spreadsheet = IOFactory::load($this->filePath); // Load the spreadsheet once
                // $sheetNames = $spreadsheet->getSheetNames();

                // foreach ($sheetNames as $sheetName) {
                //     $sheet = $spreadsheet->getSheetByName($sheetName);
                //     $headings = $sheet->toArray()[0] ?? []; // Fetch the first row for headers

                //     // Ensure all expected headings are present
                //     $missingHeadings = array_diff($this->expectedHeadings, $headings);
                //     if (!empty($missingHeadings)) {
                //         Log::error("Missing headings in sheet: {$sheetName}");
                //         throw new ExcelValidationException(
                //             "Missing headings in sheet '{$sheetName}': " . implode(', ', $missingHeadings)
                //         );
                //     }

                //     Log::info("Headings for sheet '{$sheetName}' validated successfully.");
                // }

                // Initialize total rows and JobProgress
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
                        'form_name' => 'Attendance Register Import',
                    ]
                );

                Cache::put("{$this->cacheKey}_import_progress", 0, now()->addMinutes(30));
            },



            AfterImport::class => function (AfterImport $event) {
                $user = User::find($this->submissionDetails['user_id']);
                $user->notify(new JobNotification($this->cacheKey, 'Your file has finished importing.', []));

                $status = $user->hasAnyRole([
                    'internal',
                    'organiser',
                    'admin'
                ]) ? 'approved' : 'pending';

                Submission::create([
                    'batch_no' => $this->submissionDetails['batch_no'],
                    'form_id' => $this->submissionDetails['form_id'],
                    'period_id' => $this->submissionDetails['period_month_id'],
                    'user_id' => $this->submissionDetails['user_id'],
                    'status' => $status,
                    'batch_type' => 'batch',
                    'is_complete' => 1,
                    'table_name' => 'attendance_registers',
                    'file_link' => $this->submissionDetails['file_link']
                ]);

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
                $errorMessage = $exception instanceof ValidationException ? $exception->getMessage() : 'Internal server problem';

                JobProgress::updateOrCreate(
                    ['cache_key' => $this->cacheKey],
                    [
                        'status' => 'failed',
                        'progress' => 100,
                        'error' => $errorMessage
                    ]
                );

                Log::error($exception->getMessage());
                throw new ExcelValidationException($errorMessage);
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
