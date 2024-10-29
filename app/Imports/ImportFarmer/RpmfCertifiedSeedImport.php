<?php

namespace App\Imports\ImportFarmer;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\RpmFarmerCertifiedSeed;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmfCertifiedSeedImport implements ToModel, WithHeadingRow, WithValidation,  SkipsOnFailure, WithChunkReading
{
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
            Log::error("Farmer ID not found for Certified Seed row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerCertifiedSeed record with the actual Farmer ID
        return new RpmFarmerCertifiedSeed([
            'rpmf_id' => $farmerId,
            'variety' => $row['Variety'],
            'area' => $row['Area'],
        ]);
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Certified Seed' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
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
            'Farmer ID' => 'required|exists:rpmf_certified_seed,rpmf_id', // Validate Farmer ID
            'Variety' => 'required|string|max:255',
            'Area' => 'required|numeric|min:0',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
