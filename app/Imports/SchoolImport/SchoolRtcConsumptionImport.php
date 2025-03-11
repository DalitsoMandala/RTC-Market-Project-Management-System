<?php

namespace App\Imports\SchoolImport;

use App\Models\User;
use App\Models\Submission;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\SchoolRtcConsumption;
use Illuminate\Support\Facades\Cache;
use App\Notifications\JobNotification;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use App\Traits\excelDateFormat;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\ValidationException;

HeadingRowFormatter::default('none');
class SchoolRtcConsumptionImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
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
        $schoolRecord = SchoolRtcConsumption::create([
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'school_name' => $row['School Name'],
            'date' => \Carbon\Carbon::parse($row['Date'])->format('Y-m-d'),
            'crop_cassava' => $row['Cassava Crop'],
            'crop_potato' => $row['Potato Crop'],
            'crop_sweet_potato' => $row['Sweet Potato Crop'],
            'male_count' => $row['Male Count'],
            'female_count' => $row['Female Count'],
            'total' => $row['Male Count'] + $row['Female Count'],
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
        $row['Date'] =  $this->convertExcelDate($row['Date']);
        return $row;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'School RTC Consumption' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'School Name' => 'required|string|max:255',
            'Date' => 'nullable|date|date_format:d-m-Y',
            // 'Crop' => 'nullable|string|max:255',
            'Crop Cassava' => 'nullable|boolean',
            'Crop Potato' => 'nullable|boolean',
            'Crop Sweet Potato' => 'nullable|boolean',
            'Male Count' => 'nullable|integer|min:0',
            'Female Count' => 'nullable|integer|min:0',
            // 'Total' => 'nullable|integer|min:0',
        ];
    }
    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }
}
