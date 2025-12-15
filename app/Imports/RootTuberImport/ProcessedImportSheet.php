<?php

namespace App\Imports\RootTuberImport;

use Carbon\Carbon;
use App\Models\User;
use App\Models\JobProgress;
use App\Traits\excelDateFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\ProcessedTuberImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class ProcessedImportSheet implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    protected $submissionDetails;
    protected $cacheKey;
    protected $totalRows;

    public function __construct($submissionDetails, $cacheKey, $totalRows)
    {
        $this->submissionDetails = $submissionDetails;
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
    }

    public function model(array $row)
    {

        $user = User::find($this->submissionDetails['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }




        $data = ProcessedTuberImport::create([
            'uuid' => $this->submissionDetails['batch_no'],
            'entry_border' => $row['Entry Border'],
            'reg_date' => Carbon::parse($row['Registration Date'])->format('Y-m-d'),
            'tpin' => $row['TPIN'],
            'importer_name' => $row['Importer Name'],
            'year' => $row['Year'],
            'hs_code' => $row['HS Code'],
            'tariff_description' => $row['Tariff Description'] ?? null,
            'commercial_description' => $row['Commercial Description'] ?? null,
            'package_kind' => $row['Package Kind'] ?? null,
            'packages' => $row['Number of Packages'] ?? null,
            'origin' => $row['Origin'] ?? null,
            'netweight_kgs' => $row['Net Weight (Kgs)'] ?? null,
            'foreign_currency' => $row['Foreign Currency'] ?? null,
            'currency' => $row['Currency'] ?? null,
            'exchange_rate' => $row['Exchange Rate'] ?? null,
            'value_for_duty_mwk' => $row['Value for Duty (MWK)'] ?? null,
            'description' => $row['Other Information'] ?? null,
            'user_id' => $this->submissionDetails['user_id'],
            'organisation_id' => $this->submissionDetails['organisation_id'],
            'status' => $status
        ]);

        // Cache the mapping of `att_id` to primary key if necessary
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $data;
    }


    use excelDateFormat;
    public function prepareForValidation(array $row)
    {

        if (!empty($row['Registration Date'])) {
            $row['Registration Date'] = $this->convertExcelDate($row['Registration Date']);
        }

        if (empty($row['Year']) ) {
            $row['Year'] = 0;
        }
        if(is_string($row['Year'])) {
            $row['Year'] = is_numeric($row['Year']) ? $row['Year'] : 0;
        }

        if (empty($row['Number of Packages']) || is_string($row['Number of Packages'])) {
            $row['Number of Packages'] = 0;
        }

        if (empty($row['Net Weight (Kgs)']) || is_string($row['Net Weight (Kgs)'])) {
            $row['Net Weight (Kgs)'] = 0;
        }

        if (empty($row['Foreign Currency']) || is_string($row['Foreign Currency'])) {
            $row['Foreign Currency'] = 0;
        }

        if (empty($row['Exchange Rate']) || is_string($row['Exchange Rate'])) {
            $row['Exchange Rate'] = 0;
        }

        if (empty($row['Value for Duty (MWK)']) || is_string($row['Value for Duty (MWK)'])) {
            $row['Value for Duty (MWK)'] = 0;
        }

        return $row;
    }
    public function rules(): array
    {
        return [
            'Entry Border' => 'nullable|string|max:255',
            'Registration Date' => 'nullable|date_format:d-m-Y',
            'TPIN' => 'nullable|string|max:50',
            'Importer Name' => 'nullable|string|max:255',
            'Year' => 'nullable|integer',
            'HS Code' => 'nullable|string|max:20',
            'Tariff Description' => 'nullable|string',
            'Commercial Description' => 'nullable|string',
            'Package Kind' => 'nullable|string|max:100',
            'Number of Packages' => 'nullable|integer|min:0',
            'Origin' => 'nullable|string|max:100',
            'Net Weight (Kgs)' => 'nullable|numeric|min:0',
            'Foreign Currency' => 'nullable|numeric|min:0',
            'Currency' => 'nullable|string|max:10',
            'Exchange Rate' => 'nullable|numeric|min:0',
            'Value for Duty (MWK)' => 'nullable|numeric|min:0',
            'Other Information' => 'nullable|string',
        ];
    }




    public function onFailure(Failure ...$failures)
    {

        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'PROCESSED PRODUCTS IMPORTS SHEET' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
