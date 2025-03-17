<?php

namespace App\Imports\ImportProcessor;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;

use App\Models\RpmProcessorAggregationCenter;
use App\Traits\newIDTrait;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RpmpAggregationCentersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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


        // Create the RpmProcessorAggregationCenter record
        $aggregationCenterRecord = RpmProcessorAggregationCenter::create([
            'rpmp_id' => $row['Processor ID'],
            'name' => $row['Aggregation Center Name'], // assuming "Name" is a column in the import sheet
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

    use newIDTrait;
    public function prepareForValidation(array $row)
    {
        $row['Processor ID'] = $this->validateNewIdForProcessors("processor_id_mapping", $this->cacheKey, $row, "Processor ID");
        return $row;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Aggregation Centers' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }
    public function rules(): array
    {
        return [
            'Processor ID' => 'exists:rtc_production_processors,id', // Ensure valid processor ID
            'Aggregation Center Name' => 'string|max:255', // Name of the Aggregation Center entry
        ];
    }


    public function startRow(): int
    {
        return 3; // Start reading data from row 2
    }
}
