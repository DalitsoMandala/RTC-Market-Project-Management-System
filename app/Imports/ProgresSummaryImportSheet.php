<?php

namespace App\Imports;

use App\Models\Indicator;
use App\Models\AdditionalReport;

use App\Models\SubmissionTarget;

use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Maatwebsite\Excel\Concerns\ToModel;



use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Exceptions\ExcelValidationException;
use App\Models\OrganisationTarget;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

HeadingRowFormatter::default('none');
class ProgresSummaryImportSheet implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
{
    use Importable, RegistersEventListeners;

    public $organisation_id;
    public $user_id;
    public $financial_year_id;
    public $uuid;

    public function __construct($financial_year_id, $user_id, $organisation_id, $uuid)
    {
        $this->financial_year_id = $financial_year_id;
        $this->user_id = $user_id;
        $this->organisation_id = $organisation_id;
        $this->uuid = $uuid;
    }

    public function model(array $row)
    {
        // Log::info('Processing row:', $row);

        // if (empty($row['Indicator No.']) && empty($row['Indicator']) && empty($row['Disaggregation'])) {
        //     return null; // Skip empty rows
        // }

        $indicator = Indicator::with('disaggregations')->where('indicator_name', $row['Indicator'])->first();
        if ($indicator) {
            $disaggregations = $indicator->disaggregations;
            foreach ($disaggregations as $disaggregation) {
                if ($disaggregation->name == $row['Disaggregation']) {
                    try {
                        $data = [
                            'uuid' => $this->uuid,
                            'indicator_id' => $indicator->id,
                            'indicator_disaggregation_id' => $disaggregation->id,
                            'period_month_id' => ReportingPeriodMonth::where('type', 'UNSPECIFIED')->first()->id,
                            'user_id' => $this->user_id,
                            'organisation_id' => $this->organisation_id,
                            'year_1' => $row['Y1 Achieved'] ?? 0,
                            'year_2' => $row['Y2 Achieved'] ?? 0,
                        ];

                        return AdditionalReport::updateOrCreate([
                            'indicator_id' => $indicator->id,
                            'indicator_disaggregation_id' => $disaggregation->id,
                            'period_month_id' => ReportingPeriodMonth::where('type', 'UNSPECIFIED')->first()->id,
                            'user_id' => $this->user_id,
                            'organisation_id' => $this->organisation_id,
                        ], $data);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                        throw new \Exception("An error occurred while importing data.");
                    }
                }
            }
        }

        return null;
    }



    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Progress summary' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new ExcelValidationException($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'Indicator Number' => 'required|string', // Allow null values
            'Indicator' => 'required|string',
            'Disaggregation' => 'required|string',
            // 'Y1 Target' => 'nullable|numeric|min:0',
            'Y1 Achieved' => 'nullable|numeric|min:0',
            'Y2 Target' => 'nullable|numeric|min:0',
            'Y2 Achieved' => 'nullable|numeric|min:0',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}