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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class AttendanceRegistersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
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




        $attendanceRecord = AttendanceRegister::create([
            'meetingTitle' => $row['Meeting Title'],
            'meetingCategory' => $row['Meeting Category'],
            'rtcCrop_cassava' => $row['Cassava'] ?? 0,
            'rtcCrop_potato' => $row['Potato'] ?? 0,
            'rtcCrop_sweet_potato' => $row['Sweet Potato'] ?? 0,
            'venue' => $row['Venue'],
            'district' => $row['District'],
            'startDate' => Carbon::parse($row['Start Date'])->format('Y-m-d'),
            'endDate' => Carbon::parse($row['End Date'])->format('Y-m-d'),
            'totalDays' => $row['Total Days'] ?? 1,
            'name' => $row['Name'],
            'sex' => $row['Sex'],
            'organization' => $row['Organization'],
            'designation' => $row['Designation'],
            'category' => $row['Category'],
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

        if (!empty($row['Start Date'])) {
            $row['Start Date'] = $this->convertExcelDate($row['Start Date']);
        }
        if (!empty($row['End Date'])) {
            $row['End Date'] = $this->convertExcelDate($row['End Date']);
        }

        $row['EPA'] = $row['EPA'] ?? 'NA';
        $row['Section'] = $row['Section'] ?? 'NA';
        $row['District'] = $row['District'] ?? 'NA';


        return $row;
    }
    public function rules(): array
    {
        return [
            'Meeting Title' => 'required|string|max:255',
            'Meeting Category' => 'required|string|max:255|in:Training,Meeting,Workshop',
            'Cassava' => 'boolean',
            'Potato' => 'boolean',
            'Sweet Potato' => 'boolean',
            'Venue' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Start Date' => 'required|date|date_format:d-m-Y',
            'End Date' => 'required|date|after_or_equal:Start Date|date_format:d-m-Y',
            'Total Days' => 'nullable|numeric|min:0',
            'Name' => 'required|string|max:255',
            'Sex' => 'required|in:Male,Female',
            'Organization' => 'nullable|string|max:255',
            'Designation' => 'nullable|string|max:255',
            'Category' => 'required|string|max:255|in:Farmer,Processor,Trader,Partner,Staff,Other',
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
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
