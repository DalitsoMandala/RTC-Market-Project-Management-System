<?php

namespace App\Imports\ImportProcessor;

use App\Models\RpmProcessorInterMarket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\JobProgress;
use App\Traits\excelDateFormat;
use App\Traits\newIDTrait;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmProcessorInterMarketsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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

        // Create the RpmProcessorInterMarket record
        $marketRecord = RpmProcessorInterMarket::create([
            'rpm_processor_id' => $row["Processor ID"],
            'date_recorded' => \Carbon\Carbon::parse($row['Date Recorded'])->format('Y-m-d'),
            'crop_type' => $row['Crop Type'],
            'market_name' => $row['Market Name'],
            'country' => $row['Country'],
            'date_of_maximum_sale' => \Carbon\Carbon::parse($row['Date of Maximum Sale'])->format('Y-m-d'),
            'product_type' => $row['Product Type'],
            'volume_sold_previous_period' => $row['Volume Sold Previous Period'] ?? 0,
            'financial_value_of_sales' => $row['Financial Value of Sales'] ?? 0,
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
            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    use excelDateFormat;
    use newIDTrait;
    public function prepareForValidation(array $row)
    {
        if (!empty($row['Date Recorded'])) {
            $row['Date Recorded'] = $this->convertExcelDate($row['Date Recorded']);
        }
        if (!empty($row['Date of Maximum Sale'])) {
            $row['Date of Maximum Sale'] = $this->convertExcelDate($row['Date of Maximum Sale']);
        }
        $row['Processor ID'] = $this->validateNewIdForProcessors("processor_id_mapping", $this->cacheKey, $row, "Processor ID");
        return $row;
    }
    public function rules(): array
    {
        return [
            'Processor ID' => 'exists:rtc_production_processors,id', // Ensure valid processor ID
            'Date Recorded' => 'nullable|date|date_format:d-m-Y',
            'Crop Type' => 'string|max:255',
            'Market Name' => 'nullable|string|max:255',
            'Country' => 'nullable|string|max:255',
            'Date of Maximum Sale' => 'nullable|date|date_format:d-m-Y',
            'Product Type' => 'nullable|string|max:255|in:Seed,Ware,Value added products,Fresh',
            'Volume Sold Previous Period' => 'sometimes|nullable|numeric|min:0',
            'Financial Value of Sales' => 'sometimes|nullable|numeric|min:0',
        ];
    }

    public function startRow(): int
    {
        return 3; // Skip the first row (header)
    }
}
