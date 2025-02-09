<?php

namespace App\Imports;

use App\Models\JobProgress;
use App\Helpers\ExcelValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use Maatwebsite\Excel\Events\AfterImport;
use App\Imports\ProgresSummaryImportSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;


class ProgresSummaryImport implements WithMultipleSheets, WithChunkReading, WithEvents
{

    public $filePath;
    public $expectedSheetNames = [
        'Progress summary',
    ];

    protected $totalRows = 0;
    protected $expectedHeaders = [
        'Progress summary' => [
            "Indicator Number",
            "Indicator",
            "Disaggregation",
            "Y1 Achieved",
            "Y2 Target",
            "Y2 Achieved",
        ]

    ];

    public $organisation_id;
    public $user_id;

    public $uuid;
    public $file_link;

    public function __construct($filePath = null, $user_id, $organisation_id, $uuid, $file_link)
    {
        $this->filePath = $filePath;

        $this->user_id = $user_id;
        $this->organisation_id = $organisation_id;
        $this->uuid = $uuid;
        $this->file_link = $file_link;
    }

    public function sheets(): array
    {
        return [
            'Progress summary' => new ProgresSummaryImportSheet(

                $this->user_id,
                $this->organisation_id,
                $this->uuid
            )
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





                // Check if the first sheet is blank
                $firstSheetName = $this->expectedSheetNames[0];
                $sheets = $event->reader->getTotalRows();

                foreach ($sheets as $key => $sheet) {

                    if ($sheet <= 1 && $key == $firstSheetName) {

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

                // Get total rows from all sheets and initialize JobProgress
                $rowCounts = $event->reader->getTotalRows();
                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 1) ?? 0); // excluding headers
                }, 0);
            },

            AfterImport::class => function (AfterImport $event) {

                session()->flash('success', 'Importing was successful. The report will be updated shortly.');
            },

            ImportFailed::class => function (ImportFailed $event) {

                $exception = $event->getException();

                $errorMessage = $exception->getMessage();



                Log::error($exception->getMessage());
                session()->flash('error', $errorMessage);
            }
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}