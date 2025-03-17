<?php

namespace App\Imports\ImportFarmer;

use Carbon\Carbon;
use App\Models\User;
use App\Models\JobProgress;
use App\Models\RtcProductionFarmer;
use App\Traits\excelDateFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;

use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithStartRow;

HeadingRowFormatter::default('none');
class RtcProductionFarmersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    use RegistersEventListeners;
    use Importable, SkipsFailures;
    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
        $this->data = $data;
    }

    public function model(array $row)
    {
        // Create RtcProductionFarmer record without storing the 'ID' column in the database

        $user = User::find($this->data['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }
        $farmerRecord = RtcProductionFarmer::create([
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
            'number_of_plantlets_produced_cassava' => $row['Number of Plantlets Produced Cassava'],
            'number_of_plantlets_produced_potato' => $row['Number of Plantlets Produced Potato'],
            'number_of_plantlets_produced_sweet_potato' => $row['Number of Plantlets Produced Sweet Potato'],
            'number_of_screen_house_vines_harvested' => $row['Screen House Vines Harvested'],
            'number_of_screen_house_min_tubers_harvested' => $row['Screen House Min Tubers Harvested'],
            'number_of_sah_plants_produced' => $row['SAH Plants Produced'],
            'is_registered_seed_producer' => $row['Is Registered Seed Producer'],
            'registration_number_seed_producer' => $row['Seed Producer Registration Number'],
            'registration_date_seed_producer' => \Carbon\Carbon::parse($row['Seed Producer Registration Date'])->format('Y-m-d'),
            'uses_certified_seed' => $row['Uses Certified Seed'],
            'market_segment_fresh' => $row['Market Segment Fresh'],
            'market_segment_processed' => $row['Market Segment Processed'],
            'has_rtc_market_contract' => $row['Has RTC Market Contract'],
            'total_vol_production_previous_season' => $row['Total Volume Production Previous Season'],
            'prod_value_previous_season_total' => $row['Production Value Previous Season Total'],
            'prod_value_previous_season_date_of_max_sales' => \Carbon\Carbon::parse($row['Production Value Date of Max Sales'])->format('Y-m-d'),
            'prod_value_previous_season_usd_rate' => $row['Production Value USD Rate'],
            'prod_value_previous_season_usd_value' => $row['Production Value USD Value'],
            'total_vol_irrigation_production_previous_season' => $row['Total Volume Irrigation Production Previous Season'],
            'irr_prod_value_previous_season_total' => $row['Irrigation Production Value Total'],
            'irr_prod_value_previous_season_date_of_max_sales' => \Carbon\Carbon::parse($row['Irrigation Production Date of Max Sales'])->format('Y-m-d'),
            'irr_prod_value_previous_season_usd_rate' => $row['Irrigation Production USD Rate'],
            'irr_prod_value_previous_season_usd_value' => $row['Irrigation Production USD Value'],
            'sells_to_domestic_markets' => $row['Sells to Domestic Markets'],
            'sells_to_international_markets' => $row['Sells to International Markets'],
            'uses_market_information_systems' => $row['Uses Market Information Systems'],
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

        Cache::put("farmer_id_mapping1_{$this->cacheKey}_{$row['ID']}", $farmerRecord->id, now()->addMinutes(30));
        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }
        Log::info("Processed Farmer : " . Cache::put("farmer_id_mapping1_{$this->cacheKey}_{$row['ID']}", $farmerRecord->id));
        return $farmerRecord;
    }


    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Production Farmers' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());


            throw new \Exception($errorMessage);
        }
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        $row['Date of Recruitment'] = $this->convertExcelDate($row['Date of Recruitment']);
        $row['Registration Date'] = $this->convertExcelDate($row['Registration Date']);
        $row['Seed Producer Registration Date'] = $this->convertExcelDate($row['Seed Producer Registration Date']);
        $row['Production Value Date of Max Sales'] = $this->convertExcelDate($row['Production Value Date of Max Sales']);
        $row['Irrigation Production Date of Max Sales'] = $this->convertExcelDate($row['Irrigation Production Date of Max Sales']);
        return $row;
    }


    public function rules(): array
    {
        return [
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Enterprise' => 'required|string|max:255',
            'Date of Recruitment' => 'nullable|date|date_format:d-m-Y',
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
            'Registration Date' => 'nullable|date|date_format:d-m-Y',
            'Employees Formal Female 18-35' => 'nullable|integer|min:0',
            'Employees Formal Male 18-35' => 'nullable|integer|min:0',
            'Employees Formal Male 35+' => 'nullable|integer|min:0',
            'Employees Formal Female 35+' => 'nullable|integer|min:0',
            'Employees Informal Female 18-35' => 'nullable|integer|min:0',
            'Employees Informal Male 18-35' => 'nullable|integer|min:0',
            'Employees Informal Male 35+' => 'nullable|integer|min:0',
            'Employees Informal Female 35+' => 'nullable|integer|min:0',
            'Number of Plantlets Produced Cassava' => 'nullable|integer|min:0',
            'Number of Plantlets Produced Potato' => 'nullable|integer|min:0',
            'Number of Plantlets Produced Sweet Potato' => 'nullable|integer|min:0',
            'Screen House Vines Harvested' => 'nullable|integer|min:0',
            'Screen House Min Tubers Harvested' => 'nullable|integer|min:0',
            'SAH Plants Produced' => 'nullable|integer|min:0',
            'Is Registered Seed Producer' => 'nullable|boolean',
            'Seed Producer Registration Number' => 'nullable|string|max:255',
            'Seed Producer Registration Date' => 'nullable|date|date_format:d-m-Y',
            'Uses Certified Seed' => 'nullable|boolean',
            'Market Segment Fresh' => 'nullable|boolean',
            'Market Segment Processed' => 'nullable|boolean',
            'Has RTC Market Contract' => 'nullable|boolean',
            'Total Volume Production Previous Season' => 'nullable|numeric|min:0',
            'Production Value Previous Season Total' => 'nullable|numeric|min:0',
            'Production Value Date of Max Sales' => 'nullable|date|date_format:d-m-Y',
            'Production Value USD Rate' => 'nullable|numeric|min:0',
            'Production Value USD Value' => 'nullable|numeric|min:0',
            'Total Volume Irrigation Production Previous Season' => 'nullable|numeric|min:0',
            'Irrigation Production Value Total' => 'nullable|numeric|min:0',
            'Irrigation Production Date of Max Sales' => 'nullable|date|date_format:d-m-Y',
            'Irrigation Production USD Rate' => 'nullable|numeric|min:0',
            'Irrigation Production USD Value' => 'nullable|numeric|min:0',
            'Sells to Domestic Markets' => 'nullable|boolean',
            'Sells to International Markets' => 'nullable|boolean',
            'Uses Market Information Systems' => 'nullable|boolean',
            'Sells to Aggregation Centers' => 'nullable|boolean',
            'Total Volume Aggregation Center Sales' => 'nullable|numeric|min:0'
        ];
    }




    public function startRow(): int
    {
        return 3;
    }
}
