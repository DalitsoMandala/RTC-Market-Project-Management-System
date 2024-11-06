<?php

namespace App\Imports\ImportProcessor;

use App\Models\RpmProcessorInterMarket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\JobProgress;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmProcessorInterMarketsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
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
            Log::error("Processor ID not found for International Market row: " . json_encode($row));
            return null; // Skip row if mapping is missing
        }

        // Create the RpmProcessorInterMarket record
        $marketRecord = RpmProcessorInterMarket::create([
            'rpm_processor_id' => $processorId,
            'date_recorded' => \Carbon\Carbon::parse($row['Date Recorded'])->format('Y-m-d'),
            'crop_type' => $row['Crop Type'],
            'market_name' => $row['Market Name'],
            'country' => $row['Country'],
            'date_of_maximum_sale' => \Carbon\Carbon::parse($row['Date of Maximum Sale'])->format('Y-m-d'),
            'product_type' => $row['Product Type'],
            'volume_sold_previous_period' => $row['Volume Sold Previous Period'],
            'financial_value_of_sales' => $row['Financial Value of Sales'],
            'status' => 'approved',
        ]);

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $this->totalRows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $marketRecord;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'International Markets' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());
            throw new \Exception($errorMessage);
        }
    }
    public function rules(): array
    {
        return [
            'Processor ID' => 'exists:rtc_production_processors,id', // Ensure valid processor ID
            'Date Recorded' => 'nullable|date_format:Y-m-d',
            'Crop Type' => 'string|max:255',
            'Market Name' => 'nullable|string|max:255',
            'Country' => 'nullable|string|max:255',
            'Date of Maximum Sale' => 'nullable|date_format:Y-m-d',
            'Product Type' => 'string|max:255',
            'Volume Sold Previous Period' => 'nullable|numeric|min:0',
            'Financial Value of Sales' => 'nullable|numeric|min:0',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
