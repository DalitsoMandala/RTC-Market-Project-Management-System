<?php

namespace App\Imports;

use App\Models\JobProgress;
use App\Models\SeedBeneficiary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;

use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class CropSheetImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
{
    use Importable;

    protected $cropType;
    protected $cacheKey;
    protected $totalRows;

    protected $submissionDetails;

    public function __construct(string $cropType, $submissionDetails, $cacheKey, $totalRows)
    {
        $this->cropType = $cropType;
        $this->submissionDetails = $submissionDetails;
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
    }

    public function model(array $row)
    {
        // Create SeedBeneficiary record
        $beneficiary = SeedBeneficiary::create([
            'crop' => $this->cropType,
            'district' => $row['District'],
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'name_of_aedo' => $row['Name of AEDO'],
            'aedo_phone_number' => $row['AEDO Phone Number'],
            'date' => \Carbon\Carbon::parse($row['Date'])->format('Y-m-d'),
            'name_of_recipient' => $row['Name of Recipient'],
            'village' => $row['Village'],
            'sex' => $row['Sex'],
            'age' => $row['Age'],
            'marital_status' => $row['Marital Status'],
            'hh_head' => $row['Household Head'],
            'household_size' => $row['Household Size'],
            'children_under_5' => $row['Children Under 5 in HH'],
            'variety_received' => $row['Variety Received'],
            'bundles_received' => $row['Bundles Received'],
            'phone_or_national_id' => $row['Phone / National ID'],
            'user_id' => $this->submissionDetails['user_id'],

        ]);

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $beneficiary;
    }

    public function rules(): array
    {
        return [
            'Crop' => 'required|string|in:Potato,OFSP,Cassava',
            'District' => 'required|string|max:255',
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'Name of AEDO' => 'required|string|max:255',
            'AEDO Phone Number' => 'nullable|string|max:20',
            'Date' => 'nullable|date',
            'Name of Recipient' => 'required|string|max:255',
            'Village' => 'required|string|max:255',
            'Sex' => 'required|integer|in:1,2',
            'Age' => 'required|integer|min:1',
            'Marital Status' => 'required|integer|in:1,2,3,4',
            'Household Head' => 'required|integer|in:1,2,3',
            'Household Size' => 'required|integer|min:1',
            'Children Under 5 in HH' => 'nullable|integer|min:0',
            'Variety Received' => 'required|string|max:255',
            'Bundles Received' => 'required|integer|min:1',
            'Phone / National ID' => 'required|string|max:255',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet '{$this->cropType}' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new ExcelValidationException($errorMessage);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
