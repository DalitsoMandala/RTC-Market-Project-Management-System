<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\JobProgress;
use App\Models\SeedBeneficiary;
use App\Traits\excelDateFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class CropSheetImportCassava implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
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

    public  function extractYear($value)
    {
        // Extract year pattern (supports 4-digit years or 2-digit years)
        preg_match('/\b(20\d{2}|\d{2})\b/', $value, $matches);

        if (!empty($matches)) {
            $year = $matches[0];

            // Convert two-digit year (e.g., "23") to full year (assume 20xx)
            if (strlen($year) == 2) {
                $year = "20" . $year;
            }

            // Ensure valid Carbon date
            return Carbon::createFromFormat('Y', $year)->year;
        }

        return 2023; // No valid year found
    }

    public function model(array $row)
    {

        $user = User::find($this->submissionDetails['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }
        $sex = $row['Sex'];
        if (is_numeric($sex)) {
            $sex = match ($sex) {
                1 => 'Male',
                2 => 'Female',
                3 => 'Other',
                default => $sex
            };
        }
        $row['violet'] = 0;
        $row['rosita'] = 0;
        $row['chuma'] = 0;
        $row['mwai'] = 0;
        $row['zikomo'] = 0;
        $row['thandizo'] = 0;
        $row['royal_choice'] = 0;
        $row['kaphulira'] = 0;
        $row['chipika'] = 0;
        $row['mathuthu'] = 0;
        $row['kadyaubwelere'] = 0;
        $row['sungani'] = 0;
        $row['kajiyani'] = 0;
        $row['mugamba'] = 0;
        $row['kenya'] = 0;
        $row['nyamoyo'] = 0;
        $row['anaakwanire'] = 0;
        $row['other'] = 0;

        if ($this->cropType == 'Potato') {
            $explode = explode(',', $row['Variety Received']);

            // Initialize all possible varieties to 0


            foreach ($explode as $item) {
                $variety = match (trim($item)) {
                    '1' => 'violet',
                    '2' => 'rosita',
                    '3' => 'chuma',
                    '4' => 'mwai',
                    '5' => 'zikomo',
                    '6' => 'thandizo',
                    default => 'other'
                };
                $row[$variety] = 1;
            }
        } else
        if ($this->cropType == 'OFSP') {
            $explode = explode(',', $row['Variety Received']);

            foreach ($explode as $item) {
                $variety = match (trim($item)) {
                    '1' => 'royal_choice',
                    '2' => 'kaphulira',
                    '3' => 'chipika',
                    '4' => 'mathuthu',
                    '5' => 'kadyaubwelere',
                    '6' => 'sungani',
                    '7' => 'kajiyani',
                    '8' => 'mugamba',
                    '9' => 'kenya',
                    '10' => 'nyamoyo',
                    '11' => 'anaakwanire',
                    default => 'other',
                };

                // Set the corresponding variety to 1
                $row[$variety] = 1;
            }
        }





        $dateOfAssessment = Carbon::parse($row['Date of Distribution'])->format('Y-m-d');
        // Create SeedBeneficiary record
        $beneficiary = SeedBeneficiary::updateOrCreate(
            [
                'national_id' => $row['National ID'], // Unique identifier to check existence
                'district' => $row['District'],
                'epa' => $row['EPA'],
                'section' => $row['Section'],
                'name_of_aedo' => $row['Name of AEDO'],
                'aedo_phone_number' => $row['AEDO Phone Number'],
                'date' => $dateOfAssessment,
                'name_of_recipient' => $row['Name of Recipient'],
            ],
            [
                'crop' => $this->cropType,
                'district' => $row['District'],
                'epa' => $row['EPA'],
                'section' => $row['Section'],
                'name_of_aedo' => $row['Name of AEDO'],
                'aedo_phone_number' => $row['AEDO Phone Number'],
                'date' => $dateOfAssessment,
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
                'phone_number' => $row['Phone Number'],
                'user_id' =>    $this->submissionDetails['user_id'],
                'signed' => $row['Signed'],
                'year' => $row['Year'],
                'organisation_id' => $this->submissionDetails['organisation_id'],
                'submission_period_id' => $this->submissionDetails['submission_period_id'],
                'financial_year_id' => $this->submissionDetails['financial_year_id'],
                'period_month_id' => $this->submissionDetails['period_month_id'],
                'uuid' => $this->cacheKey,
                'status' => $status, // Fixed value
                'violet' => $row['violet'],
                'rosita' =>  $row['rosita'],
                'chuma' => $row['chuma'],
                'mwai' => $row['mwai'],
                'zikomo' =>  $row['zikomo'],
                'thandizo' =>  $row['thandizo'],
                'royal_choice' => $row['royal_choice'],
                'kaphulira' => $row['kaphulira'],
                'chipika' => $row['chipika'],
                'mathuthu' => $row['mathuthu'],
                'kadyaubwelere' => $row['kadyaubwelere'],
                'sungani' => $row['sungani'],
                'kajiyani' => $row['kajiyani'],
                'mugamba' => $row['mugamba'],
                'kenya' => $row['kenya'],
                'nyamoyo' => $row['nyamoyo'],
                'anaakwanire' => $row['anaakwanire'],
                'other' => $row['other'],
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
            'District' => 'required|string|max:255',
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'Name of AEDO' => 'required|string|max:255',
            'AEDO Phone Number' => 'nullable|max:255',
            'Date of Distribution' => 'nullable|date|date_format:d-m-Y',
            'Name of Recipient' => 'required|string|max:255',
            'Village' => 'nullable|string|max:255',
            'Sex' => 'required|integer|in:Male,Female,1,2',
            'Age' => 'required|integer|min:1',
            'Marital Status' => 'nullable|integer',
            'Household Head' => 'nullable|integer|min:1',
            'Household Size' => 'nullable|integer|min:1',
            'Children Under 5 in HH' => 'nullable|integer|min:0',
            'Variety Received' => ['nullable', 'max:255'],
            'Bundles Received' => ['nullable', 'max:255'],
            'National ID' => 'nullable|max:255',
            'Phone Number' => 'nullable|max:255',
            'Signed' => 'nullable|integer:min:0',
            'Year' => 'nullable|integer'

        ];
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        $date =  $this->convertExcelDate($row['Date of Distribution']);

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
            $row['Sex'] = 'Male';
        }
        if (!$row['Marital Status'] || is_string($row['Marital Status'])) {
            $row['Marital Status'] = 1;
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





        if (!$row['Variety Received']) {
            $row['Variety Received'] = 'NA';
        }


        if (!$row['Bundles Received']) {



            $row['Bundles Received'] = 0;
        }

        if (is_string($row['Bundles Received'])) {
            $row['Bundles Received'] = 1;
        }

        if (!$row['Signed']) {
            $row['Signed'] = 0;
        }
        if (!$row['Year']) {
            $row['Year'] = 2023;
        }

        if ($row['Year']) {

            $year = $this->extractYear($row['Year']);
            $row['Year'] = $year;
        }

        $row['Date of Distribution'] = $date;
        $head = $row['Household Head'];
        if (!is_numeric($head)) {
            $head = match ($head) {

                'MHH' => 1,
                'FHH' => 2,
                'CHH' => 3,
                default => 1
            };
        }

        $row['Household Head'] = $head;



        return $row;
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

    public function batchSize(): int
    {
        return 1000;
    }
}
