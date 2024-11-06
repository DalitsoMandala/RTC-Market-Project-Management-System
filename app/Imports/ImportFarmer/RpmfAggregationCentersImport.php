<?php

namespace App\Imports\ImportFarmer;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\RpmFarmerAggregationCenter;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmfAggregationCentersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
{
    use Importable;
    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;
    protected $processedRows = 0;

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
        $this->data = $data;
    }

    public function model(array $row)
    {
        // Retrieve Farmer ID from cache using farmer_id_mapping_
        $farmerId = Cache::get("farmer_id_mapping_{$this->cacheKey}_{$row['Farmer ID']}");
        if (!$farmerId) {
            Log::error("Farmer ID not found for Aggregation Center row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerAggregationCenter record with the actual Farmer ID
        return new RpmFarmerAggregationCenter([
            'rpmf_id' => $farmerId,
            'name' => $row['Name'],
        ]);
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Area Cultivation' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());
            throw new \Exception($errorMessage);
        }
    }
    public function rules(): array
    {
        return [
            'Farmer ID' => 'exists:rpmf_aggregation_centers,rpmf_id', // Validate Farmer ID
            'Name' => 'string|max:255',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
