<?php

namespace App\Imports;

use App\Exceptions\ExcelValidationException;
use App\Models\JobProgress;
use App\Models\SeedBeneficiary;
use App\Models\User;
use App\Traits\excelDateFormat;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;

HeadingRowFormatter::default('none');

class CropSheetImportCassava implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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
        $beneficiary = SeedBeneficiary::create(
            [
                'crop' => $this->cropType,
                'district' => $row['District'],
                'epa' => $row['EPA'],
                'section' => $row['Section'],
                'name_of_aedo' => $row['Name of AEDO'],
                'aedo_phone_number' => $row['AEDO Phone Number'],
                'date' => $dateOfAssessment,
                'name_of_recipient' => $row['Name of Recipient'],
                'group_name' => $row['Group Name'],
                'village' => $row['Village'],
                'sex' => $row['Sex'],
                'age' => $row['Age'],
                'marital_status' => $row['Marital Status'],
                'hh_head' => $row['Household Head'],
                'household_size' => $row['Household Size'],
                'children_under_5' => $row['Children Under 5 in HH'],
                'variety_received' => $row['Variety Received'],
                'bundles_received' => $row['Amount of Bundles Received'],
                'phone_number' => $row['Phone Number'],
                'national_id' => $row['National ID'],
                'user_id' => $this->submissionDetails['user_id'],
                'year' => $row['Year Of Distribution'],
                'organisation_id' => $this->submissionDetails['organisation_id'],
                'submission_period_id' => $this->submissionDetails['submission_period_id'],
                'financial_year_id' => $this->submissionDetails['financial_year_id'],
                'period_month_id' => $this->submissionDetails['period_month_id'],
                'uuid' => $this->cacheKey,
                'status' => $status,  // Fixed value
                'season_type' => $row['Season Type'],
                'type_of_actor' => $row['Type of Actor'],
                'type_of_plot' => $row['Type of Plot'],
            ]
        );

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
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'Name of AEDO' => 'nullable|string|max:255',
            'AEDO Phone Number' => 'nullable|max:255',
            'Date of Distribution' => 'nullable|date|date_format:d-m-Y',
            'Year of Distribution' => 'nullable|integer',
            'Name of Recipient' => 'nullable|string|max:255',
            'Village' => 'nullable|string|max:255',
            'National ID' => 'nullable|max:255',
            'District' => 'required|string|max:255',
            'Age' => 'nullable|integer|min:1',
            'Marital Status' => 'nullable|in:Single,Married,Separated,Widowed,Polygamy',
            'Household Head' => 'nullable|in:FHH,MHH,CHH',
            'Household Size' => 'nullable|integer|min:1',
            'Children Under 5 in HH' => 'nullable|integer|min:0',
            'Sex' => 'nullable|in:Male,Female',
            'Group Name' => 'nullable|max:255',
            'Variety Received' => ['nullable', 'max:255'],
            'Amount of Bundles Received' => ['nullable', 'numeric'],
            'Phone Number' => 'nullable|max:255',
            'Season Type' => 'nullable|max:255',
            'Type of Plot' => 'nullable|in:Mother,Baby,Ordinary demonstration',
            'Type of Actor' => 'nullable|in:Caregroup,School feeding,Commercial',
        ];
    }

    use excelDateFormat;

    public function prepareForValidation(array $row)
    {
        $date = $this->convertExcelDate($row['Date of Distribution']);

        if (!$row['Age']) {
            $row['Age'] = 1;
        }

        if (!$row['National ID']) {
            $row['National ID'] = 'NA';
        }

        if (!$row['Phone Number']) {
            $row['Phone Number'] = 'NA';
        }
        if (!$row['Household Size']) {
            $row['Household Size'] = 1;
        }

        if (!$row['Sex']) {
            $row['Sex'] = 'NA';
        }

        if (!$row['Household Head']) {
            $row['Household Head'] = 1;
        }
        if (!$row['Children Under 5 in HH']) {
            $row['Children Under 5 in HH'] = 0;
        }
        if (!$row['Village']) {
            $row['Village'] = 'NA';
        }

        if (!$row['AEDO Phone Number']) {
            $row['AEDO Phone Number'] = 'NA';
        }

        if (!$row['Section']) {
            $row['Section'] = 'NA';
        }
        if (!$row['EPA']) {
            $row['EPA'] = 'NA';
        }
        if (!$row['Name of AEDO']) {
            $row['Name of AEDO'] = 'NA';
        }
        if (!$row['Name of Recipient']) {
            $row['Name of Recipient'] = 'NA';
        }
        if (!$row['District']) {
            $row['District'] = 'NA';
        }

        if (!$row['Season Type']) {
            $row['Season Type'] = 'NA';
        }

        if (!$row['Variety Received']) {
            $row['Variety Received'] = 'NA';
        }

        if (!$row['Year Of Distribution']) {
            $row['Year Of Distribution'] = 0;
        }

        $row['Date of Distribution'] = $date;
        if (($row['Type of Plot'] != 'Baby')) {
        } else if (($row['Type of Plot'] == 'Mother')) {
        }
        return $row;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet '{$this->cropType}' - Row {$failure->row()}, Field '{$failure->attribute()}': "
                . implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new ExcelValidationException($errorMessage);
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
