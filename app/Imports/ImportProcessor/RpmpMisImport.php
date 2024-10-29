<?php

namespace App\Imports\ImportProcessor;

use App\Models\RpmProcessorMarketInformationSystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\JobProgress;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmpMisImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
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
        // Retrieve the actual Processor ID using the sheet's 'ID'
        $processorId = Cache::get("processor_id_mapping_{$this->cacheKey}_{$row['Processor ID']}");
        if (!$processorId) {
            Log::error("Processor ID not found for MIS row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }



        // Create the RpmProcessorMarketInformationSystem record
        $misRecord = RpmProcessorMarketInformationSystem::create([
            'rpmp_id' => $processorId,
            'name' => $row['MIS Name'], // assuming "MIS Name" is a column in the import sheet
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
            $errorMessage = "Validation Error on sheet 'Market Information Systems' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);

            // Store the error message in JobProgress
            JobProgress::updateOrCreate(
                ['cache_key' => $this->cacheKey],
                [
                    'status' => 'failed',
                    'progress' => 100,
                    'error' => $errorMessage,
                ]
            );
        }
    }
    public function rules(): array
    {
        return [
            'Processor ID' => 'required|exists:rtc_production_processors,id', // Ensure valid processor ID
            'MIS Name' => 'required|string|max:255', // MIS Name of the MIS entry
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
