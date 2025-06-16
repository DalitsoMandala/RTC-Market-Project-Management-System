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
            'mem_female_18_35' => $row['Members Female 18-35'] ?? 0,
            'mem_male_18_35' => $row['Members Male 18-35'] ?? 0,
            'mem_male_35_plus' => $row['Members Male 35+'] ?? 0,
            'mem_female_35_plus' => $row['Members Female 35+'] ?? 0,
            'category' => $row['Category'],
            'establishment_status' => $row['Establishment Status'] ?? 'Old',
            'is_registered' => $row['Is Registered'] ?? 0,
            'registration_body' => $row['Registration Body'],
            'registration_number' => $row['Registration Number'],
            'registration_date' => \Carbon\Carbon::parse($row['Registration Date'])->format('Y-m-d'),
            'emp_formal_female_18_35' => $row['Employees Formal Female 18-35'] ?? 0,
            'emp_formal_male_18_35' => $row['Employees Formal Male 18-35'] ?? 0,
            'emp_formal_male_35_plus' => $row['Employees Formal Male 35+'] ?? 0,
            'emp_formal_female_35_plus' => $row['Employees Formal Female 35+'] ?? 0,
            'emp_informal_female_18_35' => $row['Employees Informal Female 18-35'] ?? 0,
            'emp_informal_male_18_35' => $row['Employees Informal Male 18-35'] ?? 0,
            'emp_informal_male_35_plus' => $row['Employees Informal Male 35+'] ?? 0,
            'emp_informal_female_35_plus' => $row['Employees Informal Female 35+'] ?? 0,
            'area_under_cultivation' => $row['Area Under Cultivation'] ?? 0,
            'is_registered_seed_producer' => $row['Is Registered Seed Producer'] ?? 0,
            'uses_certified_seed' => $row['Uses Certified Seed'] ?? 0,
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
        //   Log::info("Processed Recruit : " . Cache::get("recruitment_id_mapping_{$this->cacheKey}_{$row['ID']}") . "- {$farmerRecord->id} here");
        return $farmerRecord;
    }


    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'RTC Actor Recruitment' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());


            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        if (!empty($row['Date of Recruitment'])) {
            $row['Date of Recruitment'] = $this->convertExcelDate($row['Date of Recruitment']);
        }

        if (!empty($row['Registration Date'])) {
            $row['Registration Date'] = $this->convertExcelDate($row['Registration Date']);
        }



        $row['EPA'] = $row['EPA'] ?? 'NA';
        $row['Section'] = $row['Section'] ?? 'NA';
        $row['District'] = $row['District'] ?? 'NA';
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
            'Members Female 18-35' => 'required|numeric|min:0',
            'Members Male 18-35' => 'nullable|numeric|min:0',
            'Members Male 35+' => 'nullable|numeric|min:0',
            'Members Female 35+' => 'nullable|numeric|min:0',
            'Category' => 'nullable|string|max:255|in:Early generation seed producer,Seed multiplier,RTC producer',
            'Establishment Status' => 'nullable|string|in:New,Old',
            'Is Registered' => 'nullable|boolean',
            'Registration Body' => 'nullable|max:255',
            'Registration Number' => 'nullable|max:255',
            'Registration Date' => 'nullable|date|date_format:d-m-Y',
            'Employees Formal Female 18-35' => 'nullable|numeric|min:0',
            'Employees Formal Male 18-35' => 'nullable|numeric|min:0',
            'Employees Formal Male 35+' => 'nullable|numeric|min:0',
            'Employees Formal Female 35+' => 'nullable|numeric|min:0',
            'Employees Informal Female 18-35' => 'nullable|numeric|min:0',
            'Employees Informal Male 18-35' => 'nullable|numeric|min:0',
            'Employees Informal Male 35+' => 'nullable|numeric|min:0',
            'Employees Informal Female 35+' => 'nullable|numeric|min:0',
            'Is Registered Seed Producer' => 'nullable|boolean',
            'Uses Certified Seed' => 'nullable|boolean',

        ];
    }




    public function startRow(): int
    {
        return 3;
    }
}
