<?php

namespace App\Imports\ImportFarmer;

use App\Models\FarmerSeedRegistration;
use App\Models\JobProgress;
use App\Traits\excelDateFormat;
use App\Traits\newIDTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class SeedServicesUnitImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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
        // Retrieve Farmer ID from cache based on sheet ID mapping

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerMarketInformationSystem record with the acdfbtual Farmer ID
        return new FarmerSeedRegistration([
            'farmer_id' => $row['Farmer ID'],
            'reg_date' => \Carbon\Carbon::parse($row['Registration Date'])->format('Y-m-d'),
            'reg_no' => $row['Registration Number'],
            'variety' => $row['Variety'],
        ]);
    }

    use newIDTrait;
    use excelDateFormat;

    public function prepareForValidation(array $row)
    {
        $row['Farmer ID'] = $row['ID'];
        $row['Farmer ID'] = $this->validateNewIdForRecruits('farmer_id_mapping1', $this->cacheKey, $row, 'Farmer ID');
        $row['Registration Date'] = $this->convertExcelDate($row['Registration Date']);
        return $row;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Seed Services Unit' - Row {$failure->row()}, Field '{$failure->attribute()}': "
                . implode(', ', $failure->errors());
            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'ID' => 'exists:rtc_production_farmers,id',  // Validate Farmer ID
            'Registration Date' => 'nullable|date|date_format:d-m-Y',
            'Registration Number' => 'nullable|max:255',
            'Variety' => 'nullable|max:255',
        ];
    }

    public function startRow(): int
    {
        return 3;
    }
}