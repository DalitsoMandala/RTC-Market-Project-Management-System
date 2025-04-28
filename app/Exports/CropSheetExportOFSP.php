<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\SeedBeneficiary;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CropSheetExportOFSP extends CropSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{

    public $validationTypes = [
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'Name of AEDO' => 'Required, Text',
        'AEDO Phone Number' => 'Text',
        'Date of Distribution' => 'Date (dd-mm-yyyy)',
        'Year Of Distribution' => 'Number',
        'Name of Recipient' => 'Required, Text',
        'Village' => 'Text',
        'National ID' => 'Text',
        'District' => 'Required, Text',
        'Age' => 'Required, Number (>=1)',
        'Marital Status' => 'Number',
        'Household Head' => 'Number (>=1)',
        'Household Size' => 'Number (>=1)',
        'Children Under 5 in HH' => 'Number (>=0)',
        'Sex' => 'Required, Male/Female or 1/2',
        'Group Name' => 'Text',
        'Type of Actor' => 'Number',
        'Name of Plot' => 'Number',
        'Variety Received' => 'Text, (IF Multiple separate by commas)',
        'Amount of Bundles Received' => 'Number(>=0)',
        'Phone Number' => 'Text',
        'Season Type' => 'Text, (Choose One)'
    ];




    public function headings(): array
    {
        return [
            array_keys($this->validationTypes),
            array_values($this->validationTypes)
        ];
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



                $dropdownOptions = [

                    'Rainfed',
                    'Winter',

                ];
                $this->setDataValidations($dropdownOptions, 'W3', $sheet);
            },
        ];
    }

    public function title(): string
    {
        return $this->cropType;
    }
}
