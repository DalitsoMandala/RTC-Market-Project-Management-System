<?php

namespace App\Exports\AttendanceImport;

use App\Models\AttendanceRegister;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceRegistersExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the necessary columns to be included in the export
        return AttendanceRegister::select(

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



        )->get();
    }

    public function headings(): array
    {
        return [

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

        ];
    }

    public function title(): string
    {
        return 'Attendance Registers';
    }
}
