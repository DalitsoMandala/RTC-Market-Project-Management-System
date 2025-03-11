<?php

namespace App\Imports\ImportFarmer;

use App\Models\JobProgress;
use App\Models\RpmFarmerDomMarket;
use App\Traits\excelDateFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmFarmerDomMarketsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
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
        // Retrieve Farmer ID from cache using farmer_id_mapping1_
        $farmerId = Cache::get("farmer_id_mapping1_{$this->cacheKey}_{$row['Farmer ID']}");
        if (!$farmerId) {
            Log::error("Farmer ID not found for Domestic Market row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerDomMarket record with the actual Farmer ID
        return new RpmFarmerDomMarket([
            'rpm_farmer_id' => $farmerId,
            'date_recorded' => \Carbon\Carbon::parse($row['Date Recorded'])->format('Y-m-d'),
            'crop_type' => $row['Crop Type'],
            'market_name' => $row['Market Name'],
            'district' => $row['District'],
            'date_of_maximum_sale' => \Carbon\Carbon::parse($row['Date of Maximum Sale'])->format('Y-m-d'),
            'product_type' => $row['Product Type'],
            'volume_sold_previous_period' => $row['Volume Sold Previous Period'],
            'financial_value_of_sales' => $row['Financial Value of Sales'],
        ]);
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        $row['Date of Maximum Sale'] =  $this->convertExcelDate($row['Date of Maximum Sale']);
        $row['Date Recorded'] = $this->convertExcelDate($row['Date Recorded']);
        return $row;
    }
    public function rules(): array
    {
        return [
            'Farmer ID' => 'exists:rpm_farmer_dom_markets,rpm_farmer_id', // Validate Farmer ID
            'Date Recorded' => 'nullable|date|date_format:d-m-Y',
            'Crop Type' => 'required|string|max:255',
            'Market Name' => 'required|string|max:255',
            'District' => 'nullable|string|max:255',
            'Date of Maximum Sale' => 'nullable|date|date_format:d-m-Y',
            'Product Type' => 'nullable|string|max:255',
            'Volume Sold Previous Period' => 'nullable|numeric|min:0',
            'Financial Value of Sales' => 'nullable|numeric|min:0',
        ];
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Domestic Markets' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);

            throw new \Exception($errorMessage);
        }
    }
    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
