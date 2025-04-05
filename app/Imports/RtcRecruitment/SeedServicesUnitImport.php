<?php

namespace App\Imports\RtcRecruitment;

use App\Traits\newIDTrait;
use App\Models\JobProgress;
use Illuminate\Support\Collection;
use App\Models\RecruitSeedRegistration;
use App\Traits\excelDateFormat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;

class SeedServicesUnitImport implements ToModel
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
            'recruitment_id' => $row['Recruitment ID'],
            'reg_date' => $row['Registration Date'],
            'reg_no' => $row['Registration Number'],
            'variety' => $row['Variety'],
        ]);
    }
    use newIDTrait;
    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        $row['Recruitment ID'] = $this->validateNewIdForFarmers("recruitment_id_mapping1_", $this->cacheKey, $row, "Recruitment ID");
        $row['Registration Date'] = $this->convertExcelDate($row['Registration Date']);
        return $row;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Seed Services Unit' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());
            throw new \Exception($errorMessage);
        }
    }
    public function rules(): array
    {
        return [

            'Rectruitment ID' => 'exists:recruit_seed_registrations,id',
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
