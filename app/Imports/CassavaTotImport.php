<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\CassavaTot;
use App\Models\MotherPlot;
use App\Traits\excelDateFormat;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');


class CassavaTotImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
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
            'Name' => 'nullable|string|max:255',
            'Gender' => 'nullable|string|max:255',
            'Age Group' => 'nullable|string|max:255',
            'District' => 'nullable|string|max:255',
            'EPA' => 'nullable|string|max:255',
            'Position' => 'nullable|string|max:255',
            'Phone Number' => 'nullable|max:255',
            'Email Address' => 'nullable|email|max:255',
        ];
    }

    public function model(array $row)
    {



        return  CassavaTot::updateOrCreate([

            'name' => $row['Name'],
            'gender' => $row['Gender'],
            'district' => $row['District'],
            'epa' => $row['EPA'],

        ], [
            'name' => $row['Name'],
            'gender' => $row['Gender'],
            'age_group' => $row['Age Group'],
            'district' => $row['District'],
            'epa' => $row['EPA'],
            'position' => $row['Position'],
            'phone_numbers' => $row['Phone Number'],
            'email_address' => $row['Email Address'],
        ]);
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {

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