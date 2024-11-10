<?php

namespace App\Imports\HouseholdImport;

use Ramsey\Uuid\Uuid;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;
use App\Models\HouseholdRtcConsumption;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');
class HouseholdSheetImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithEvents, SkipsOnFailure
{
    use Importable, RegistersEventListeners;

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
        // Create HouseholdRtcConsumption record without storing the 'ID' column in the database


        $householdRecord = HouseholdRtcConsumption::create([
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'enterprise' => $row['Enterprise'],
            'date_of_assessment' => \Carbon\Carbon::parse($row['Date of Assessment'])->format('Y-m-d'),
            'actor_type' => $row['Actor Type (Farmer, Trader, etc.)'],
            'rtc_group_platform' => $row['RTC Group/Platform'],
            'producer_organisation' => $row['Producer Organisation'],
            'actor_name' => $row['Actor Name'],
            'age_group' => $row['Age Group'],
            'sex' => $row['Sex'],
            'phone_number' => $row['Phone Number'],
            'household_size' => $row['Household Size'],
            'under_5_in_household' => $row['Under 5 in Household'],
            'rtc_consumers' => $row['RTC Consumers (Total)'],
            'rtc_consumers_potato' => $row['RTC Consumers - Potato'],
            'rtc_consumers_sw_potato' => $row['RTC Consumers - Sweet Potato'],
            'rtc_consumers_cassava' => $row['RTC Consumers - Cassava'],
            'rtc_consumption_frequency' => $row['RTC Consumption Frequency'],
            'uuid' => $this->cacheKey,
            'user_id' => $this->data['user_id'],
            'organisation_id' => $this->data['organisation_id'],
            'submission_period_id' => $this->data['submission_period_id'],
            'financial_year_id' => $this->data['financial_year_id'],
            'period_month_id' => $this->data['period_month_id'],
            'status' => 'approved'
        ]);

        // Map the 'ID' from the sheet to the actual primary key
        Cache::put("household_id_mapping_{$this->cacheKey}_{$row['ID']}", $householdRecord->id, now()->addMinutes(30));

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $householdRecord;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Household Data' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'ID' => 'required|distinct',
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Enterprise' => 'required|string|max:255',
            'Date of Assessment' => 'nullable|date_format:Y-m-d', // Ensure it's a valid date format
            'Actor Type (Farmer, Trader, etc.)' => 'nullable|string|max:255',
            'RTC Group/Platform' => 'nullable|string|max:255',
            'Producer Organisation' => 'nullable|string|max:255',
            'Actor Name' => 'nullable|string|max:255',
            'Age Group' => 'nullable|string|max:50', // Customize as needed based on expected age group values
            'Sex' => 'nullable|string|in:Male,Female,Other', // Limit to specific options
            'Phone Number' => 'nullable|max:255', // Phone number format with optional +, numbers, spaces, or dashes
            'Household Size' => 'nullable|integer|min:1', // Minimum 1 household member
            'Under 5 in Household' => 'nullable|integer|min:0', // Minimum 0
            'RTC Consumers (Total)' => 'nullable|integer|min:0', // Minimum 0 consumers
            'RTC Consumers - Potato' => 'nullable|integer|min:0', // Minimum 0 consumers for Potato
            'RTC Consumers - Sweet Potato' => 'nullable|integer|min:0', // Minimum 0 consumers for Sweet Potato
            'RTC Consumers - Cassava' => 'nullable|integer|min:0', // Minimum 0 consumers for Cassava
            'RTC Consumption Frequency' => 'nullable|integer|min:0|max:365', // Assume frequency is within a year (max 365)

            // Additional field validations as required
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
