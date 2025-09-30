<?php

namespace App\Imports\ImportFarmer;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\RpmFarmerAreaCultivation;
use App\Traits\newIDTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmfAreaCultivationImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerAreaCultivation record with the actual Farmer ID
        return new RpmFarmerAreaCultivation([
            'rpmf_id' => $row['Farmer ID'],
            'variety' => $row['Variety'],
            'area' => $row['Area'] ?? 0,
        ]);
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Area Cultivation' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }
    use newIDTrait;
    public function prepareForValidation(array $row)
    {
        $row['Farmer ID'] = $this->validateNewIdForFarmers("farmer_id_mapping1", $this->cacheKey, $row, "Farmer ID");

        return $row;
    }
    public function rules(): array
    {
        return [
            'Farmer ID' => 'exists:rtc_production_farmers,id', // Validate Farmer ID
            'Variety' => 'string|max:255',
            'Area' => 'sometimes|nullable|numeric|min:0',
        ];
    }


    public function startRow(): int
    {
        return 3;
    }
}
