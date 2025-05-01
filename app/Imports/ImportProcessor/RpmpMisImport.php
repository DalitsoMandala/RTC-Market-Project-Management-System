<?php

namespace App\Imports\ImportProcessor;

use App\Models\JobProgress;
use App\Models\RpmProcessorMarketInformationSystem;
use App\Traits\newIDTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmpMisImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->data = $data;
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
    }

    public function model(array $row)
    {
        // Create the RpmProcessorMarketInformationSystem record
        $misRecord = RpmProcessorMarketInformationSystem::create([
            'rpmp_id' => $row['Processor ID'],
            'name' => $row['Name'],  // assuming "MIS Name" is a column in the import sheet
        ]);

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $this->totalRows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $misRecord;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Market Information Systems' - Row {$failure->row()}, Field '{$failure->attribute()}': "
                . implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'Processor ID' => 'exists:rtc_production_processors,id',  // Ensure valid processor ID
            'Name' => 'string|max:255',  // MIS Name of the MIS entry
        ];
    }

    use newIDTrait;

    public function prepareForValidation(array $row)
    {
        $row['Processor ID'] = $this->validateNewIdForProcessors('processor_id_mapping', $this->cacheKey, $row, 'Processor ID');
        return $row;
    }

    public function startRow(): int
    {
        return 3;  // Start reading data from the 3rd row
    }
}
