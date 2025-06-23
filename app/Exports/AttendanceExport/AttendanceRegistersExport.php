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
    use \App\Traits\FormEssentials;
    public $template = false;
    protected $validationTypes = [];
    public function __construct($template = false)
    {

        $this->template = $template;
        $this->validationTypes = $this->forms['Attendance Register Form']['Attendance Register'];
    }

    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
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

                    'Training',
                    'Meeting',
                    'Workshop',

                ]; // Includes an empty option

                $this->setDataValidations($dropdownOptions, 'B3', $sheet);
                $dropdownOptions = [

                    'Farmer',
                    'Processor',
                    'Trader',
                    'Partner',
                    'Staff',
                    'Aggregator',
                    'Transporter',
                    'Other'

                ]; // Includes an empty option
                $this->setDataValidations($dropdownOptions, 'O3', $sheet);
            },
        ];
    }


    public function headings(): array
    {
        return [

            array_keys($this->validationTypes),
            array_values($this->validationTypes)

        ];
    }

    public function title(): string
    {
        return 'Attendance Register';
    }
}
