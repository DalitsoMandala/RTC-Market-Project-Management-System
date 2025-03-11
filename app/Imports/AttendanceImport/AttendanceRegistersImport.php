<?php

namespace App\Imports\AttendanceImport;

use Carbon\Carbon;
use App\Models\User;
use App\Models\JobProgress;
use App\Traits\excelDateFormat;
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

        $user = User::find($this->submissionDetails['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }


        $sex = $row['Sex'];
        if (is_numeric($sex)) {
            $sex = match ($sex) {
                1 => 'Male',
                2 => 'Female',

                default => 'Male',
            };
        } elseif (is_string($sex)) {
            $sex = strtolower($sex);
            $sex = match ($sex) {
                'm' => 'Male',
                'f' => 'Female',

                default => 'Male',
            };
        }


        $attendanceRecord = AttendanceRegister::create([
            'meetingTitle' => $row['Meeting Title'],
            'meetingCategory' => $row['Meeting Category'],
            'rtcCrop_cassava' => $row['RTC Crop Cassava'],
            'rtcCrop_potato' => $row['RTC Crop Potato'],
            'rtcCrop_sweet_potato' => $row['RTC Crop Sweet Potato'],
            'venue' => $row['Venue'],
            'district' => $row['District'],
            'startDate' => Carbon::parse($row['Start Date'])->format('Y-m-d'),
            'endDate' => Carbon::parse($row['End Date'])->format('Y-m-d'),
            'totalDays' => $row['Total Days'],
            'name' => $row['Name'],
            'sex' => $sex,
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
            'status' => $status,
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


    use excelDateFormat;
    public function prepareForValidation(array $row)
    {
        $row['Start Date'] =   $this->convertExcelDate($row['Start Date']);
        $row['End Date'] =  $this->convertExcelDate($row['End Date']);

        if (is_string($row['Meeting Category'])) {
            $split = explode(',', $row['Meeting Category']);
            $row['Meeting Category'] = trim($split[0]);
        } else {
            $category = $row['Meeting Category'];
            $row['Meeting Category'] = match ($category) {
                1 => 'Training',
                2 => 'Meeting',
                3 => 'Workshop',
                default => 'Other',
            };
        }

        if (!$row['Total Days']) {
            $row['Total Days'] = 1;
        }

        if (!$row['Sex']) {
            $row['Sex'] = 1;
        }
        return $row;
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
            'Start Date' => 'required|date|date_format:d-m-Y',
            'End Date' => 'required|date|after_or_equal:Start Date|date_format:d-m-Y',
            'Total Days' => 'required|integer|min:1',
            'Name' => 'required|string|max:255',
            'Sex' => 'required|in:Male,Female,1,2,M,F',
            'Organization' => 'nullable|string|max:255',
            'Designation' => 'nullable|string|max:255',
            'Phone Number' => 'nullable|max:255',
            'Email' => 'nullable|max:255',
        ];
    }





    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Attendance Registers' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
                implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new \Exception($errorMessage);
        }
    }
}
