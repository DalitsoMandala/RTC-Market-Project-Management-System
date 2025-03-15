<?php

namespace App\Imports\HouseholdImport;

use App\Models\MainFood;
use App\Models\JobProgress;
use App\Models\MainFoodHrc;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class MainFoodSheetImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, WithStartRow
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

    public function startRow(): int
    {
        return 3;
    }
    public function model(array $row)
    {
        // Retrieve actual Household ID using the sheet's 'ID' from Household Data
        $householdId = Cache::get("household_id_mapping_{$this->cacheKey}_{$row['Household ID']}");
        if (!$householdId) {
            Log::error("Household ID not found for Main Food row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the MainFoodHrc record with the actual Household ID
        return new MainFoodHrc([
            'hrc_id' => $householdId,
            'name' => $row['Main Food Name'],
        ]);
    }
    public function rules(): array
    {
        return [
            'Household ID' => 'exists:household_rtc_consumption,id', // Ensure valid household ID
            'Main Food Name' => 'string',
        ];
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Main Food Data' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());
            throw new \Exception($errorMessage);
        }
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
