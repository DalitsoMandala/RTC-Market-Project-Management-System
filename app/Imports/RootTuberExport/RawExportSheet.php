<?php

namespace App\Imports\RootTuberExport;

use Carbon\Carbon;
use App\Models\User;
use App\Models\JobProgress;
use App\Models\RawTuberExport;
use App\Traits\excelDateFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class RawExportSheet implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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




        $data = RawTuberExport::create([
            'uuid' => $this->submissionDetails['batch_no'],
            'reg_date' => Carbon::parse($row['Registration Date'])->format('Y-m-d'),
            'year' => $row['Year'],
            'exporter_name' => $row['Exporter Name'],
            'consignee_name' => $row['Consignee Name'] ?? null,
            'quantity' => $row['Quantity'] ?? null,
            'package_kind' => $row['Package Kind'] ?? null,
            'hs_code' => $row['HS Code'],
            'goods_description' => $row['Goods Description'] ?? null,
            'commercial_goods_description' => $row['Commercial Goods Description'] ?? null,
            'origin_country' => $row['Origin Country'] ?? null,
            'exit_border' => $row['Exit Border'] ?? null,
            'destination_country' => $row['Destination Country'] ?? null,
            'netweight_kgs' => $row['Net Weight (Kgs)'] ?? null,
            'export_value_mwk' => $row['Export Value (MWK)'] ?? null,
            'description' => $row['Other Information'] ?? null,
            'user_id' => $this->submissionDetails['user_id'],
            'organisation_id' => $this->submissionDetails['organisation_id'],
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

        if (empty($row['Year'])) {
            $row['Year'] = 0;
        }
        if (is_string($row['Year'])) {
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
            'Registration Date' => 'nullable|date_format:d-m-Y',
            'Year' => 'nullable|integer',
            'Exporter Name' => 'nullable|string|max:255',
            'Consignee Name' => 'nullable|string|max:255',
            'Quantity' => 'nullable|integer|min:0',
            'Package Kind' => 'nullable|string|max:100',
            'HS Code' => 'nullable|string|max:20',
            'Goods Description' => 'nullable|string',
            'Commercial Goods Description' => 'nullable|string',
            'Origin Country' => 'nullable|string|max:100',
            'Exit Border' => 'nullable|string|max:100',
            'Destination Country' => 'nullable|string|max:100',
            'Net Weight (Kgs)' => 'nullable|numeric|min:0',
            'Export Value (MWK)' => 'nullable|numeric|min:0',
            'Other Information' => 'nullable|string',
        ];
    }




    public function onFailure(Failure ...$failures)
    {

        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'RAW PRODUCTS EXPORTS SHEET' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
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
