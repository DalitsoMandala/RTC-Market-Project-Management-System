<?php

namespace App\Imports\MarketImport;

use App\Models\User;
use App\Models\MarketData;
use App\Models\JobProgress;
use App\Traits\excelDateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class MarketDataImport implements ToModel,  WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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




        $record = MarketData::create([
            'uuid' => $this->cacheKey,
            'entry_date' => Carbon::parse($row['Entry Month'])->format('Y-m-d'),
            'off_taker_name_vehicle_reg_number' => $row['Off-taker Name/Vehicle Reg Number'],
            'trader_contact' => $row['Trader Contact'],
            'buyer_location' => $row['Buyer Location'],
            'variety_demanded' => trim($row['Variety Demanded']),
            'quality_size' => $row['Quality/Size'],
            'quantity' => $row['Quantity'],
            'units' => $row['Units'],
            'estimated_demand_kg' => $row['Estimated Demand (Kg)'],
            'agreed_price_per_kg' => $row['Agreed Price per Kg (MWK)'],
            'market_ordered_from' => $row['Market Ordered From'],
            'final_market' => $row['Final Market'],
            'final_market_district' => $row['Final Market District'],
            'final_market_country' => $row['Final Market Country'],
            'supply_frequency' => $row['Supply Frequency'],
            'estimated_total_value_mwk' => $row['Estimated Total Value (MWK)'],
            'estimated_total_value_usd' => $row['Estimated Total Value (USD)'],
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

        return $record;
    }


    use excelDateFormat;
    public function prepareForValidation(array $row)
    {

        if (!empty($row['Entry Month'])) {
            $row['Entry Month'] = $this->convertExcelDate($row['Entry Month']);
        }

        if (empty($row['Estimated Demand (Kg)'])) {
            $row['Estimated Demand (Kg)'] = 0;
        }

        if (empty($row['Agreed Price per Kg (MWK)'])) {
            $row['Agreed Price per Kg (MWK)'] = 0;
        }

        if (empty($row['Estimated Total Value (MWK)'])) {
            $row['Estimated Total Value (MWK)'] = 0;
        }

        if (empty($row['Estimated Total Value (USD)'])) {
            $row['Estimated Total Value (USD)'] = 0;
        }



        return $row;
    }
    public function rules(): array
    {
        return [
            'Entry Month' => 'nullable|date|date_format:d-m-Y',
            'Off-taker Name/Vehicle Reg Number' => 'nullable|max:255',
            'Vehicle Reg Number' => 'nullable|string|max:255',
            'Trader Contact' => 'nullable|max:255',
            'Buyer Location' => 'nullable|string|max:255',
            'Variety Demanded' => 'nullable|string|max:255',
            'Quality/Size' => 'nullable|string|max:255',
            'Quantity' => 'nullable|numeric|min:0',
            'Units' => 'nullable|max:255',
            'Estimated Demand (Kg)' => 'nullable|numeric|min:0',
            'Agreed Price per Kg (MWK)' => 'nullable|numeric|min:0',
            'Market Ordered From' => 'nullable|string|max:255',
            'Final Market' => 'nullable|string|max:255',
            'Final Market District' => 'nullable|string|max:255',
            'Final Market Country' => 'nullable|string|max:255',
            'Supply Frequency' => 'nullable|max:255',
            'Estimated Total Value (MWK)' => 'nullable|numeric|min:0',
            'Estimated Total Value (USD)' => 'nullable|numeric|min:0',
        ];
    }





    public function onFailure(Failure ...$failures)
    {

        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Marketing Monthly Report' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
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
