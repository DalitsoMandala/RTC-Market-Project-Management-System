<?php

namespace App\Imports\RtcRecruitment;

use App\Traits\newIDTrait;
use App\Models\JobProgress;
use App\Traits\excelDateFormat;
use Illuminate\Support\Collection;
use App\Models\RecruitSeedRegistration;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class SeedServicesUnitImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;
    protected $processedRows = 0;

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
        $this->data = $data;
    }

    public function model(array $row)
    {
        // Retrieve Farmer ID from cache based on sheet ID mapping

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        // Create the RpmFarmerMarketInformationSystem record with the actual Farmer ID
        return new RecruitSeedRegistration([
            'recruitment_id' => $row['ID'],
            'reg_date' => \Carbon\Carbon::parse($row['Registration Date'])->format('Y-m-d'),
            'reg_no' => $row['Registration Number'],
            'variety' => $row['Variety'],
        ]);
    }
    use newIDTrait;
    use excelDateFormat;
    public function prepareForValidation(array $row)
    {

        $row['ID'] = $this->validateNewIdForRecruits("recruitment_id_mapping", $this->cacheKey, $row, "ID");
        if (!empty($row['Registration Date'])) {
            $row['Registration Date'] = $this->convertExcelDate($row['Registration Date']);
        }

        return $row;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Seed Services Unit' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());
            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }
    public function rules(): array
    {
        return [

            'ID' => 'exists:recruitments,id',
            'Registration Date' => 'nullable|date|date_format:d-m-Y',
            'Registration Number' => 'nullable|max:255',
            'Variety' => 'nullable|max:255',
        ];
    }


    public function startRow(): int
    {
        return 3;
    }
}
