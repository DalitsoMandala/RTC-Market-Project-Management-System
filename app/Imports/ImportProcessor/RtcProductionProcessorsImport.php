<?php

namespace App\Imports\ImportProcessor;

use App\Models\User;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\RtcProductionProcessor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class RtcProductionProcessorsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
{
    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->data = $data;
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Production Processors' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }
    public function model(array $row)
    {
        // Create a new RtcProductionProcessor record
        $user = User::find($this->data['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }
        $processorRecord = RtcProductionProcessor::create([
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'enterprise' => $row['Enterprise'],
            'date_of_recruitment' => \Carbon\Carbon::parse($row['Date of Recruitment'])->format('Y-m-d'),
            'name_of_actor' => $row['Name of Actor'],
            'name_of_representative' => $row['Name of Representative'],
            'phone_number' => $row['Phone Number'],
            'type' => $row['Type'],
            'approach' => $row['Approach'],
            'sector' => $row['Sector'],
            'mem_female_18_35' => $row['Members Female 18-35'],
            'mem_male_18_35' => $row['Members Male 18-35'],
            'mem_male_35_plus' => $row['Members Male 35+'],
            'mem_female_35_plus' => $row['Members Female 35+'],
            'group' => $row['Group'],
            'establishment_status' => $row['Establishment Status'],
            'is_registered' => $row['Is Registered'],
            'registration_body' => $row['Registration Body'],
            'registration_number' => $row['Registration Number'],
            'registration_date' => \Carbon\Carbon::parse($row['Registration Date'])->format('Y-m-d'),
            'emp_formal_female_18_35' => $row['Employees Formal Female 18-35'],
            'emp_formal_male_18_35' => $row['Employees Formal Male 18-35'],
            'emp_formal_male_35_plus' => $row['Employees Formal Male 35+'],
            'emp_formal_female_35_plus' => $row['Employees Formal Female 35+'],
            'emp_informal_female_18_35' => $row['Employees Informal Female 18-35'],
            'emp_informal_male_18_35' => $row['Employees Informal Male 18-35'],
            'emp_informal_male_35_plus' => $row['Employees Informal Male 35+'],
            'emp_informal_female_35_plus' => $row['Employees Informal Female 35+'],
            'market_segment_fresh' => $row['Market Segment Fresh'],
            'market_segment_processed' => $row['Market Segment Processed'],
            'has_rtc_market_contract' => $row['Has RTC Market Contract'],
            'total_vol_production_previous_season' => $row['Total Volume Production Previous Season'],
            'prod_value_previous_season_total' => $row['Production Value Previous Season Total'],
            'prod_value_previous_season_date_of_max_sales' => \Carbon\Carbon::parse($row['Date of Max Sales'])->format('Y-m-d'),
            'prod_value_previous_season_usd_rate' => $row['USD Rate'],
            'prod_value_previous_season_usd_value' => $row['USD Value'],
            'sells_to_domestic_markets' => $row['Sells to Domestic Markets'],
            'sells_to_international_markets' => $row['Sells to International Markets'],
            'uses_market_information_systems' => $row['Uses Market Info Systems'],
            'sells_to_aggregation_centers' => $row['Sells to Aggregation Centers'],
            'total_vol_aggregation_center_sales' => $row['Total Volume Aggregation Center Sales'],
            'uuid' => $this->data['batch_no'],
            'user_id' => $this->data['user_id'],
            'organisation_id' => $this->data['organisation_id'],
            'submission_period_id' => $this->data['submission_period_id'],
            'financial_year_id' => $this->data['financial_year_id'],
            'period_month_id' => $this->data['period_month_id'],
            'status' => $status
        ]);

        // Cache the mapping of 'ID' to primary key
        Cache::put("processor_id_mapping_{$this->cacheKey}_{$row['ID']}", $processorRecord->id, now()->addMinutes(30));

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $this->totalRows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $processorRecord;
    }

    public function rules(): array
    {
        return [
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Enterprise' => 'required|string|max:255',
            'Date of Recruitment' => 'nullable|date_format:Y-m-d',
            'Name of Actor' => 'nullable|string|max:255',
            'Name of Representative' => 'nullable|string|max:255',
            'Phone Number' => 'nullable|max:255',
            'Type' => 'nullable|string|max:255',
            'Approach' => 'nullable|string|max:255',
            'Sector' => 'nullable|string|max:255',
            'Members Female 18-35' => 'nullable|integer|min:0',
            'Members Male 18-35' => 'nullable|integer|min:0',
            'Members Male 35+' => 'nullable|integer|min:0',
            'Members Female 35+' => 'nullable|integer|min:0',
            'Group' => 'nullable|string|max:255',
            'Establishment Status' => 'nullable|string|in:New,Old',
            'Is Registered' => 'nullable|boolean',
            'Registration Body' => 'nullable|string|max:255',
            'Registration Number' => 'nullable|string|max:255',
            'Registration Date' => 'nullable|date_format:Y-m-d',
            'Employees Formal Female 18-35' => 'nullable|integer|min:0',
            'Employees Formal Male 18-35' => 'nullable|integer|min:0',
            'Employees Formal Male 35+' => 'nullable|integer|min:0',
            'Employees Formal Female 35+' => 'nullable|integer|min:0',
            'Employees Informal Female 18-35' => 'nullable|integer|min:0',
            'Employees Informal Male 18-35' => 'nullable|integer|min:0',
            'Employees Informal Male 35+' => 'nullable|integer|min:0',
            'Employees Informal Female 35+' => 'nullable|integer|min:0',
            'Market Segment Fresh' => 'nullable|boolean',
            'Market Segment Processed' => 'nullable|boolean',
            'Has RTC Market Contract' => 'nullable|boolean',
            'Total Volume Production Previous Season' => 'nullable|numeric|min:0',
            'Production Value Previous Season Total' => 'nullable|numeric|min:0',
            'Date of Max Sales' => 'nullable|date_format:Y-m-d',
            'USD Rate' => 'nullable|numeric|min:0',
            'USD Value' => 'nullable|numeric|min:0',
            'Sells to Domestic Markets' => 'nullable|boolean',
            'Sells to International Markets' => 'nullable|boolean',
            'Uses Market Info Systems' => 'nullable|boolean',
            'Sells to Aggregation Centers' => 'nullable|boolean',
            'Total Volume Aggregation Center Sales' => 'nullable|numeric|min:0'
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
