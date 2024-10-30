<?php

namespace App\Imports\AttendanceImport;

use App\Models\JobProgress;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;

use App\Exceptions\ExcelValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
HeadingRowFormatter::default('none');
class AttendanceRegistersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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
        $attendanceRecord = AttendanceRegister::create([
            'meetingTitle' => $row['Meeting Title'],
            'meetingCategory' => $row['Meeting Category'],
            'rtcCrop_cassava' => $row['RTC Crop Cassava'],
            'rtcCrop_potato' => $row['RTC Crop Potato'],
            'rtcCrop_sweet_potato' => $row['RTC Crop Sweet Potato'],
            'venue' => $row['Venue'],
            'district' => $row['District'],
            'startDate' => $row['Start Date'],
            'endDate' => $row['End Date'],
            'totalDays' => $row['Total Days'],
            'name' => $row['Name'],
            'sex' => $row['Sex'],
            'organization' => $row['Organization'],
            'designation' => $row['Designation'],
            'phone_number' => $row['Phone Number'],
            'email' => $row['Email'],
            'user_id' => $this->submissionDetails['user_id'],
            'submission_period_id' => $this->submissionDetails['submission_period_id'],
            'organisation_id' => $this->submissionDetails['organisation_id'],
            'financial_year_id' => $this->submissionDetails['financial_year_id'],
            'period_month_id' => $this->submissionDetails['period_month_id'],
            'uuid' => $this->cacheKey,
            'status' => 'pending',
        ]);

        // Cache the mapping of `att_id` to primary key if necessary
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $attendanceRecord;
    }

    public function rules(): array
    {
        return [
            'Meeting Title' => 'required|string|max:255',
            'Meeting Category' => 'required|string|max:255',
            'RTC Crop Cassava' => 'boolean',
            'RTC Crop Potato' => 'boolean',
            'RTC Crop Sweet Potato' => 'boolean',
            'Venue' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Start Date' => 'required|date',
            'End Date' => 'required|date|after_or_equal:Start Date',
            'Total Days' => 'required|integer|min:1',
            'Name' => 'required|string|max:255',
            'Sex' => 'required|string|in:Male,Female,Other',
            'Organization' => 'nullable|string|max:255',
            'Designation' => 'nullable|string|max:255',
            'Phone Number' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:255',
        ];
    }





    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Attendance Registers' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);

            // Store the error message in JobProgress
            JobProgress::updateOrCreate(
                ['cache_key' => $this->cacheKey],
                [
                    'status' => 'failed',
                    'progress' => 100,
                    'error' => $errorMessage,
                ]
            );
        }
    }
}
