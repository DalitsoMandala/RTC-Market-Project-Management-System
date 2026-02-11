<?php

namespace App\Imports;

use App\Models\Submission;
use App\Models\JobProgress;
use App\Traits\FormEssentials;
use App\Helpers\ExcelValidator;
use App\Models\AdditionalReport;
use App\Traits\ChecksBlankSheets;
use App\Models\ProgressSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Events\AfterImport;
use App\Imports\ProgresSummaryImportSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class ProgresSummaryImport implements WithMultipleSheets, WithChunkReading, WithEvents
{
    use FormEssentials, ChecksBlankSheets;
    public $filePath;
    public $expectedSheetNames = [
        'Progress summary',
    ];

    protected $totalRows = 0;
    protected $expectedHeaders = [];

    public $organisation_id;
    public $user_id;

    public $uuid;
    public $file_link;
    public $table_name;
    public $description;

    public function __construct($filePath = null, $submmited_user_id, $report_organisation_id, $uuid, $file_link, $table_name, $description)
    {
        $this->filePath = $filePath;

        $this->user_id = $submmited_user_id;
        $this->organisation_id = $report_organisation_id;
        $this->uuid = $uuid;
        $this->file_link = $file_link;
        $this->table_name = $table_name;
        $this->description = $description;

        foreach ($this->expectedSheetNames as $sheetName) {
            $this->expectedHeaders[$sheetName] = array_values($this->forms['Progress summary Form'][$sheetName]);
        }
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
                $rowCounts = $event->reader->getTotalRows();
                $this->assertBlankSheetRules(
                    rowCounts: $rowCounts,
                    required: [
                        'Progress summary' => 2,
                    ],
                    optional: [

                    ],

                );

                $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                    return $sum + (($rowCounts[$sheetName] - 1) ?? 0); // excluding headers
                }, 0);
            },

            AfterImport::class => function (AfterImport $event) {
                ProgressSubmission::create([
                    'submitted_user_id' => $this->user_id,
                    'report_organisation_id' => $this->organisation_id,
                    'batch_no' => $this->uuid,
                    'file_link' => $this->file_link,
                    'table_name' => $this->table_name,
                    'description' => $this->description,
                    'status' => 'active',

                ]);

                Artisan::call('update:information');
            },

            ImportFailed::class => function (ImportFailed $event) {

                $exception = $event->getException();

                $errorMessage = $exception->getMessage();

                AdditionalReport::where('uuid', $this->uuid)->delete();

                Log::error($exception->getMessage());
                session()->flash('error', $errorMessage);
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
