<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\JobProgress;
use App\Models\SeedBeneficiary;
use App\Traits\excelDateFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\UserErrorException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class CropSheetImportOFSP implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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

    public function extractYear($value)
    {
        // Extract year pattern (supports 4-digit years or 2-digit years)
        preg_match('/\b(20\d{2}|\d{2})\b/', $value, $matches);

        if (!empty($matches)) {
            $year = $matches[0];

            // Convert two-digit year (e.g., "23") to full year (assume 20xx)
            if (strlen($year) == 2) {
                $year = '20' . $year;
            }

            // Ensure valid Carbon date
            return Carbon::createFromFormat('Y', $year)->year;
        }

        return 2023;  // No valid year found
    }

    public function model(array $row)
    {
        $user = User::find($this->submissionDetails['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }

        $dateOfAssessment = Carbon::parse($row['Date of Distribution'])->format('Y-m-d');
        // Create SeedBeneficiary record
        $beneficiary = SeedBeneficiary::create([
            'crop' => (string) $this->cropType,
            'district' => (string) $row['District'],
            'epa' => (string) $row['EPA'],
            'section' => (string) $row['Section'],
            'name_of_aedo' => (string) $row['Name of AEDO'],
            'aedo_phone_number' => (string) $row['AEDO Phone Number'],
            'date' =>  $dateOfAssessment,
            'name_of_recipient' => (string) $row['Name of Recipient'],
            'group_name' => (string) $row['Group Name'],
            'village' => (string) $row['Village'],
            'sex' => (string) $row['Sex'],
            'age' => (float) $row['Age'],
            'marital_status' => (string) ($row['Marital Status'] ?? ''),
            'hh_head' => (string) ($row['Household Head'] ?? ''),
            'household_size' => (float) ($row['Household Size'] ?? 0),
            'children_under_5' => (float) ($row['Children Under 5 in HH'] ?? 0),
            'variety_received' => strtolower((string) $row['Variety Received']),
            'bundles_received' => (float) ($row['Amount of Bundles Received'] ?? 0),
            'phone_number' => (string) $row['Phone Number'],
            'national_id' => (string) $row['National ID'],
            'user_id' => (float) $this->submissionDetails['user_id'],
            'year' => (string) $row['Year Of Distribution'],
            'organisation_id' => (float) $this->submissionDetails['organisation_id'],
            'submission_period_id' => (float) $this->submissionDetails['submission_period_id'],
            'financial_year_id' => (float) $this->submissionDetails['financial_year_id'],
            'period_month_id' => (float) $this->submissionDetails['period_month_id'],
            'uuid' => (string) $this->cacheKey,
            'status' => (string) $status,  // Fixed value
            'season_type' => (string) $row['Season Type'],
            'type_of_actor' => (string) $row['Type of Actor'],
            'type_of_plot' => (string) $row['Type of Plot'],
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
            // 'Crop' => 'required|string|in:Potato,OFSP,Cassava',
            'EPA' => 'nullable|string|max:255',
            'Section' => 'nullable|string|max:255',
            'Name of AEDO' => 'nullable|string|max:255',
            'AEDO Phone Number' => 'nullable|max:255',
            'Date of Distribution' => 'nullable|date|date_format:d-m-Y',
            'Year of Distribution' => 'nullable|numeric',
            'Name of Recipient' => 'nullable|string|max:255',
            'Village' => 'nullable|string|max:255',
            'National ID' => 'nullable|max:255',
            'District' => 'required|string|max:255',
            'Age' => 'nullable|numeric|min:1',
            'Marital Status' => 'nullable|in:Single,Married,Separated,Widowed,Polygamy,Divorced',
            'Household Head' => 'nullable|in:FHH,MHH,CHH',
            'Household Size' => 'nullable|numeric|min:0',
            'Children Under 5 in HH' => 'nullable|numeric|min:0',
            'Sex' => 'nullable|in:Male,Female',
            'Group Name' => 'nullable|max:255',
            'Variety Received' => ['nullable', 'max:255'],
            'Amount of Bundles Received' => ['nullable', 'numeric'],
            'Phone Number' => 'nullable|max:255',
            'Season Type' => 'nullable|max:255',
            'Tyoe of Plot' => 'nullable|in:Mother,Baby,Ordinary demonstration',
            'Type of Actor' => 'nullable|in:Caregroup,School feeding,Commercial',
        ];
    }

    use excelDateFormat;

    public function prepareForValidation(array $row)
    {
        $date = $this->convertExcelDate($row['Date of Distribution']);


        if (!$row['National ID']) {
            $row['National ID'] = '';
        }

        if (!$row['Phone Number']) {
            $row['Phone Number'] = '';
        }
        if (!$row['Household Size']) {
            $row['Household Size'] = 0;
        }




        if (!$row['Children Under 5 in HH']) {
            $row['Children Under 5 in HH'] = 0;
        }
        if (!$row['Village']) {
            $row['Village'] = '';
        }

        if (!$row['AEDO Phone Number']) {
            $row['AEDO Phone Number'] = '';
        }


        if (!$row['Name of AEDO']) {
            $row['Name of AEDO'] = '';
        }
        if (!$row['Name of Recipient']) {
            $row['Name of Recipient'] = '';
        }
        $row['EPA'] = $row['EPA'] ?? '';
        $row['Section'] = $row['Section'] ?? '';
        $row['District'] = $row['District'] ?? '';

        if (!$row['Season Type']) {
            $row['Season Type'] = '';
        }

        if (!$row['Variety Received']) {
            $row['Variety Received'] = '';
        }

        if (!$row['Year Of Distribution']) {
            $row['Year Of Distribution'] = 0;
        }

        $row['Date of Distribution'] = $date;
        $row['Season Type'] = $row['Season Type'] ?? 'Rainfed';

        if (!$row['Children Under 5 in HH']) {
            $row['Children Under 5 in HH'] = 0;
        }

        // Ensure string fields have defaults
        $row['EPA'] = (string)($row['EPA'] ?? '');
        $row['Section'] = (string)($row['Section'] ?? '');
        $row['District'] = (string)($row['District'] ?? '');

        return $row;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet '{$this->cropType}' - Row {$failure->row()}, Field '{$failure->attribute()}': "
                . implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new UserErrorException($errorMessage);
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
