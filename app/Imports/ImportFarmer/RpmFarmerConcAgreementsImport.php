<?php

namespace App\Imports\ImportFarmer;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\RpmFarmerConcAgreement;
use App\Traits\excelDateFormat;
use App\Traits\newIDTrait;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmFarmerConcAgreementsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerConcAgreement record with the actual Farmer ID
        return new RpmFarmerConcAgreement([
            'rpm_farmer_id' => $row['Farmer ID'],
            'date_recorded' => \Carbon\Carbon::parse($row['Date Recorded'])->format('Y-m-d'),
            'partner_name' => $row['Partner Name'],
            'country' => $row['Country'],
            'date_of_maximum_sale' => \Carbon\Carbon::parse($row['Date of Maximum Sale'])->format('Y-m-d'),
            'product_type' => $row['Product Type'],
            'volume_sold_previous_period' => $row['Volume Sold Previous Period'],
            'financial_value_of_sales' => $row['Financial Value of Sales'],
        ]);
    }


    use excelDateFormat;
    use newIDTrait;

    public function prepareForValidation(array $row)
    {
        $row['Date of Maximum Sale'] =  $this->convertExcelDate($row['Date of Maximum Sale']);
        $row['Date Recorded'] =  $this->convertExcelDate($row['Date Recorded']);
        $row['Farmer ID'] = $this->validateNewIdForFarmers("farmer_id_mapping1", $this->cacheKey, $row, "Farmer ID");

        return $row;
    }
    public function rules(): array
    {
        return [
            'Farmer ID' => 'exists:rtc_production_farmers,id', // Validate Farmer ID
            'Date Recorded' => 'nullable|date|date_format:d-m-Y',
            'Partner Name' => 'required|string|max:255',
            'Country' => 'nullable|string|max:255',
            'Date of Maximum Sale' => 'nullable|date|date_format:d-m-Y',
            'Product Type' => 'nullable|string|max:255',
            'Volume Sold Previous Period' => 'nullable|numeric|min:0',
            'Financial Value of Sales' => 'nullable|numeric|min:0',
        ];
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Contractual Agreements' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
