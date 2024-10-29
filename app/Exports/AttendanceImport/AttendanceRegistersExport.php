<?php

namespace App\Exports;

use App\Models\AttendanceRegister;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceRegistersExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public function collection()
    {
        // Select only the necessary columns to be included in the export
        return AttendanceRegister::select(
            'att_id',
            'meetingTitle',
            'meetingCategory',
            'rtcCrop_cassava',
            'rtcCrop_potato',
            'rtcCrop_sweet_potato',
            'venue',
            'district',
            'startDate',
            'endDate',
            'totalDays',
            'name',
            'sex',
            'organization',
            'designation',
            'phone_number',
            'email',
            'user_id',
            'submission_period_id',
            'organisation_id',
            'financial_year_id',
            'period_month_id',
            'uuid',
            'status'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Attendance ID',
            'Meeting Title',
            'Meeting Category',
            'RTC Crop Cassava',
            'RTC Crop Potato',
            'RTC Crop Sweet Potato',
            'Venue',
            'District',
            'Start Date',
            'End Date',
            'Total Days',
            'Name',
            'Sex',
            'Organization',
            'Designation',
            'Phone Number',
            'Email',
            'User ID',
            'Submission Period ID',
            'Organisation ID',
            'Financial Year ID',
            'Period Month ID',
            'UUID',
            'Status'
        ];
    }

    public function title(): string
    {
        return 'Attendance Registers';
    }
}
