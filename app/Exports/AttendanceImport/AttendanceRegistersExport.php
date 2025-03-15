<?php

namespace App\Exports\AttendanceExport;

use Carbon\Carbon;
use App\Models\AttendanceRegister;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceRegistersExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;
    public $template = false;
    protected $validationTypes = [
        'Meeting Title' => 'Required, Text',
        'Meeting Category' => 'Required, Text, (Choose one option)',
        'Cassava' => 'Boolean (1/0, true/false)',
        'Potato' => 'Boolean (1/0, true/false)',
        'Sweet Potato' => 'Boolean (1/0, true/false)',
        'Venue' => 'Required, Text',
        'District' => 'Required, Text',
        'Start Date' => 'Required, Date (dd-mm-yyyy)',
        'End Date' => 'Required, Date (dd-mm-yyyy), After or equal to Start Date',
        'Total Days' => 'Required, Number (>=1)',
        'Name' => 'Required, Text',
        'Sex' => 'Required, Male/Female',
        'Organization' => 'Text',
        'Designation' => 'Text',
        'Category' => 'Required, Text, (Choose one option)',
        'Phone Number' => 'Text',
        'Email' => 'Text',
    ];

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
        $data = AttendanceRegister::select(

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
            'category',
            'phone_number',
            'email',



        )->get();

        $data->transform(function ($row) {
            $startDate = Carbon::parse($row['startDate'])->format('d-m-Y');
            $endDate = Carbon::parse($row['endDate'])->format('d-m-Y');
            $row['startDate'] = $startDate;
            $row['endDate'] = $endDate;
            return $row;
        });

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                // Make the first row (header) bold
                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                // Set background color for the second row (A2:ZZ2)
                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => 'FF0000'], // Red text
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFC5'], // Pink background
                    ],

                ]);

                $sheet = $event->sheet->getDelegate();

                // Define the dropdown options
                $dropdownOptions = [
                    '',
                    'Training',
                    'Meeting',
                    'Workshop',

                ]; // Includes an empty option

                $this->setDataValidations($dropdownOptions, 'B3', $sheet);
                $dropdownOptions = [
                    '',
                    'Farmer',
                    'Processor',
                    'Trader',
                    'Partner',
                    'Staff'

                ]; // Includes an empty option
                $this->setDataValidations($dropdownOptions, 'O3', $sheet);
            },
        ];
    }


    public function headings(): array
    {
        return [

            [
                'Meeting Title',
                'Meeting Category',
                'Cassava',
                'Potato',
                'Sweet Potato',
                'Venue',
                'District',
                'Start Date',
                'End Date',
                'Total Days',
                'Name',
                'Sex',
                'Organization',
                'Designation',
                'Category',
                'Phone Number',
                'Email',
            ],
            array_values($this->validationTypes)

        ];
    }

    public function title(): string
    {
        return 'Attendance Registers';
    }
}
