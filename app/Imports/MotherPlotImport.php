<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\MotherPlot;
use App\Traits\excelDateFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');


class MotherPlotImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
{
    use Importable;

    protected $cropType;
    protected $cacheKey;
    protected $totalRows;

    protected $submissionDetails;

    public function __construct($submissionDetails, $cacheKey, $totalRows)
    {

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

    public function rules(): array
    {
        return [
            'District' => 'nullable|string|max:255',
            'EPA' => 'nullable|string|max:255',
            'Section' => 'nullable|string|max:255',
            'Village' => 'nullable|string|max:255',
            'GPS S' => 'nullable|max:255',
            'GPS E' => 'nullable|max:255',
            'Elevation' => 'sometimes|nullable|numeric|min:0',
            'Season' => 'nullable|in:1,2', // 1=Rainfed, 2=Winter
            'Date of Planting' => 'nullable|date|date_format:d-m-Y', // Ensure date is in YYYY-MM-DD format
            'Name of Farmer' => 'nullable|string|max:255',
            'Sex' => 'required|integer|in:Male,Female,1,2',
            'Nat ID / Phone #' => 'nullable|string|max:255',
            'Variety received' => 'nullable|string|max:255', // Comma-separated values (e.g., "1,2,3")
        ];
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


        $date_of_planting = $row['Date of Planting'];
        if ($date_of_planting) {
            $date_of_planting = Carbon::createFromFormat('d-m-Y', $date_of_planting)->format('Y-m-d');
        }

        return  MotherPlot::updateOrCreate([

            'district' => $row['District'],
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'village' => $row['Village'],
            'name_of_farmer' => $row['Name of Farmer'],
            'sex' => $row['Sex'],


        ], [
            'district' => $row['District'],
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'village' => $row['Village'],
            'gps_s' => $row['GPS S'],
            'gps_e' => $row['GPS E'],
            'elevation' => $row['Elevation'],
            'season' => $row['Season'],
            'date_of_planting' => $date_of_planting,
            'name_of_farmer' => $row['Name of Farmer'],
            'sex' => $row['Sex'],
            'nat_id_phone_number' => $row['Nat ID / Phone #'],
            'variety_received' => $row['Variety Received'],
        ]);
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        $row['Date of Planting'] = $this->convertExcelDate($row['Date of Planting']);
        if (!$row['Nat ID / Phone #']) {
            $row['Nat ID / Phone #'] = '';
        }

        if (!$row['Variety Received']) {
            $row['Variety Received'] = 1;
        }

        if (!$row['Sex']) {
            $row['Sex'] = 1;
        }

        if (!$row['Elevation']) {
            $row['Elevation'] = 1;
        }

        if (!$row['GPS S']) {
            $row['GPS S'] = 1;
        }

        if (!$row['GPS E']) {
            $row['GPS E'] = 1;
        }

        if (!$row['EPA']) {
            $row['EPA'] = '';
        }
        if (!$row['District']) {
            $row['District'] = '';
        }
        if (!$row['Section']) {
            $row['Section'] = '';
        }
        if (!$row['Village']) {
            $row['Village'] = '';
        }

        if (!$row['Season']) {
            $row['Season'] = 1;
        }
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
