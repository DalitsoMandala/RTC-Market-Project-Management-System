<?php

namespace App\Imports\HouseholdImport;

use Carbon\Carbon;
use App\Models\User;
use Ramsey\Uuid\Uuid;

use App\Models\JobProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Models\HouseholdRtcConsumption;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
    public function prepareForValidation(array $row)
    {
        if (!empty($row['Date of Assessment']) && is_numeric($row['Date of Assessment'])) {
            // Convert Excel serial date to d-m-Y before validation
            $row['Date of Assessment'] = Carbon::instance(Date::excelToDateTimeObject($row['Date of Assessment']))->format('d-m-Y');
        } elseif (!empty($row['Date of Assessment'])) {
            try {
                $row['Date of Assessment'] = Carbon::createFromFormat('d-m-Y', $row['Date of Assessment'])->format('d-m-Y');
            } catch (\Exception $e) {
                \Log::error('Date conversion failed', ['date' => $row['Date of Assessment'], 'error' => $e->getMessage()]);
                // Set default value if the date is invalid
                $row['Date of Assessment'] = Carbon::now()->format('d-m-Y');
            }
        } else {
            // Set default value if the date is empty
            $row['Date of Assessment'] = Carbon::now()->format('d-m-Y');
        }

        return $row;
    }
    public function model(array $row)
    {
        // Create HouseholdRtcConsumption record without storing the 'ID' column in the database

        $user = User::find($this->data['user_id']);
        $status = 'pending';
        if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }

        $sex = $row['Sex'];
        if (is_numeric($sex)) {
            $sex = match ($sex) {
                1 => 'Male',
                2 => 'Female',
                3 => 'Other',
                default => $sex
            };
        }
        $dateOfAssessment = Carbon::parse($row['Date of Assessment'])->format('Y-m-d');


        $householdRecord = HouseholdRtcConsumption::create([
            'epa' => $row['EPA'],
            'section' => $row['Section'] ?? '',
            'district' => $row['District'],
            'enterprise' => $row['Enterprise'],
            'date_of_assessment' => $dateOfAssessment,
            'actor_type' => $row['Actor Type (Farmer, Trader, etc.)'],
            'rtc_group_platform' => $row['RTC Group/Platform'],
            'producer_organisation' => $row['Producer Organisation'],
            'actor_name' => $row['Actor Name'],
            'age_group' => $row['Age Group'],
            'sex' => $sex,
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
            'status' => $status
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
            \Log::info('Validation Error', [
                'row_number' => $failure->row(),
                'failed_field' => $failure->attribute(),
                'error_message' => $failure->errors(),
                'row_data' => $failure->values(),
            ]);
            $errorMessage = "Validation Error on sheet 'Household Data' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            throw new \Exception($errorMessage);
        }
    }

    public function rules(): array
    {
        return [
            'ID' => 'required|distinct|integer',
            'EPA' => 'required|string|max:255',
            'Section' => 'nullable|string|max:255',
            'District' => 'required|string|max:255',
            'Enterprise' => 'required|string|max:255',
            'Date of Assessment' => 'nullable|date_format:d-m-Y', // Ensure it's a valid date format
            'Actor Type (Farmer, Trader, etc.)' => 'nullable|string|max:255',
            'RTC Group/Platform' => 'nullable|string|max:255',
            'Producer Organisation' => 'nullable|string|max:255',
            'Actor Name' => 'nullable|string|max:255',
            'Age Group' => 'nullable|string|max:50', // Customize as needed based on expected age group values
            'Sex' => 'nullable|in:Male,Female,Other,1,2,3', // Limit to specific options
            'Phone Number' => 'nullable|max:255', // Phone number format with optional +, numbers, spaces, or dashes
            'Household Size' => 'nullable|integer|min:0', // Minimum 1 household member
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