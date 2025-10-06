<?php

namespace App\Imports\RtcConsumption;

use App\Models\JobProgress;
use App\Models\RtcConsumption;
use App\Traits\excelDateFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class RtcConsumptionImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    use Importable;


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
        // Create SchoolRtcConsumption record
        $schoolRecord = RtcConsumption::create([
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'entity_name' => $row['Entity Name'],
            'entity_type' => $row['Entity Type'],
            'date' => \Carbon\Carbon::parse($row['Date'])->format('Y-m-d'),
            'crop_cassava' => $row['Cassava Crop'] ?? 0,
            'crop_potato' => $row['Potato Crop'] ?? 0,
            'crop_sweet_potato' => $row['Sweet Potato Crop'] ?? 0,
            'male_count' => $row['Male Count'] ?? 0,
            'female_count' => $row['Female Count'] ?? 0,
            'total' => ($row['Male Count'] + $row['Female Count']) ?? 0,
            'number_of_households' => $row['Number of Households'] ?? 0,
            'uuid' => $this->cacheKey,
            'user_id' => $this->data['user_id'],
            'organisation_id' => $this->data['organisation_id'],
            'submission_period_id' => $this->data['submission_period_id'],
            'financial_year_id' => $this->data['financial_year_id'],
            'period_month_id' => $this->data['period_month_id'],
            'status' => 'approved'
        ]);
        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $schoolRecord;
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        if (!empty($row['Date'])) {
            $row['Date'] =  $this->convertExcelDate($row['Date']);
        }

        $row['EPA'] = $row['EPA'] ?? '';
        $row['Section'] = $row['Section'] ?? '';
        $row['District'] = $row['District'] ?? '';

        return $row;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'School Rtc Consumption' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Entity Name' => 'required|string|max:255',
            'Entity Type' => 'required|string|max:255',
            'Date' => 'nullable|date|date_format:d-m-Y',
            // 'Crop' => 'nullable|string|max:255',
            'Cassava Crop' => 'nullable|boolean',
            'Potato Crop' => 'nullable|boolean',
            'Sweet Potato Crop' => 'nullable|boolean',
            'Male Count' => 'required|numeric|min:0',
            'Female Count' => 'required|numeric|min:0',
            'Number of Households' => 'sometimes|nullable|numeric|min:0',
            // 'Total' => 'required|numeric|min:0',
        ];
    }

    public function startRow(): int
    {
        return 3;
    }
}
