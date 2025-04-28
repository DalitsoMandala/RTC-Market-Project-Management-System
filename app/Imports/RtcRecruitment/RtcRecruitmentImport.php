<?php

namespace App\Imports\RtcRecruitment;

use App\Models\User;
use App\Models\JobProgress;
use App\Models\Recruitment;
use App\Traits\excelDateFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

HeadingRowFormatter::default('none');

class RtcRecruitmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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
        $farmerRecord = Recruitment::create([

            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'enterprise' => $row['Enterprise'],
            'date_of_recruitment' => \Carbon\Carbon::parse($row['Date of Recruitment'])->format('Y-m-d'),
            'name_of_actor' => $row['Name of Actor'],
            'name_of_representative' => $row['Name of Representative'],
            'phone_number' => $row['Phone Number'],
            'type' => $row['Type'],
            'group' => $row['Group'],
            'approach' => $row['Approach'],
            'sector' => $row['Sector'],
            'mem_female_18_35' => $row['Members Female 18-35'],
            'mem_male_18_35' => $row['Members Male 18-35'],
            'mem_male_35_plus' => $row['Members Male 35+'],
            'mem_female_35_plus' => $row['Members Female 35+'],
            'category' => $row['Category'],
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
            'area_under_cultivation' => $row['Area Under Cultivation'],
            'is_registered_seed_producer' => $row['Is Registered Seed Producer'],
            'registration_number_seed_producer' => $row['Seed Producer Registration Number'],
            'registration_date_seed_producer' => \Carbon\Carbon::parse($row['Seed Producer Registration Date'])->format('Y-m-d'),
            'uses_certified_seed' => $row['Uses Certified Seed'],
            'uuid' => $this->data['batch_no'],
            'user_id' => $this->data['user_id'],
            'organisation_id' => $this->data['organisation_id'],
            'submission_period_id' => $this->data['submission_period_id'],
            'financial_year_id' => $this->data['financial_year_id'],
            'period_month_id' => $this->data['period_month_id'],
            'status' => $status
        ]);


        // Cache the mapping of 'ID' to primary key

        Cache::put("recruitment_id_mapping_{$this->cacheKey}_{$row['ID']}", $farmerRecord->id, now()->addMinutes(30));
        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }
        Log::info("Processed Recruit : " . Cache::get("recruitment_id_mapping_{$this->cacheKey}_{$row['ID']}") . "- {$farmerRecord->id} here");
        return $farmerRecord;
    }


    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'RTC Actor Recruitment' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
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
        return $row;
    }


    public function rules(): array
    {
        return [
            'ID' => 'required|numeric',
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Enterprise' => 'required|string|max:255|in:Cassava,Potato,Sweet potato',
            'Date of Recruitment' => 'required|date|date_format:d-m-Y',
            'Name of Actor' => 'nullable|string|max:255',
            'Name of Representative' => 'nullable|string|max:255',
            'Phone Number' => 'nullable|max:255',
            'Type' => 'nullable|string|max:255|in:Farmers,Processors,Traders,Aggregators,Transporters',
            'Group' => 'nullable|string|max:255|in:Producer organization (PO),Large scale farm,Large scale processor,Small medium enterprise (SME),Other',
            'Approach' => 'nullable|string|max:255',
            'Sector' => 'nullable|string|max:255|in:Private,Public',
            'Members Female 18-35' => 'nullable|integer|min:0',
            'Members Male 18-35' => 'nullable|integer|min:0',
            'Members Male 35+' => 'nullable|integer|min:0',
            'Members Female 35+' => 'nullable|integer|min:0',
            'Category' => 'nullable|string|max:255|in:Early generation seed producer,Seed multiplier,RTC producer',
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
            'Is Registered Seed Producer' => 'nullable|boolean',
            'Seed Producer Registration Number' => 'nullable|string|max:255',
            'Seed Producer Registration Date' => 'nullable|date|date_format:d-m-Y',
            'Uses Certified Seed' => 'nullable|boolean',

        ];
    }




    public function startRow(): int
    {
        return 3;
    }
}
