<?php

namespace App\Imports\ImportProcessor;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;

use App\Models\RpmProcessorAggregationCenter;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmpAggregationCentersImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
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
            Log::error("Processor ID not found for Aggregation Center row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }

        // Create the RpmProcessorAggregationCenter record
        $aggregationCenterRecord = RpmProcessorAggregationCenter::create([
            'rpmp_id' => $processorId,
            'name' => $row['Name'], // assuming "Name" is a column in the import sheet
        ]);

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $this->totalRows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $aggregationCenterRecord;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Aggregation Centers' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
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
            'Name' => 'required|string|max:255', // Name of the Aggregation Center entry
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
