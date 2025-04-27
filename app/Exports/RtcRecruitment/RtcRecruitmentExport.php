<?php

namespace App\Exports\RtcRecruitment;

use App\Models\Recruitment;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcRecruitmentExport implements WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{


    use ExportStylingTrait;




    protected $rowNumber = 0; // Start counting from 1
    public $template;
    protected $validationTypes = [
        'ID' => 'Required, Unique,  Number',
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'District' => 'Required, Text',
        'Enterprise' => 'Required, Text',
        'Date of Recruitment' => 'Date (dd-mm-yyyy)',
        'Name of Actor' => 'Text',
        'Name of Representative' => 'Text',
        'Phone Number' => 'Text',
        'Type' => 'Text, (Choose one option)',
        'Group' => 'Text, (Choose one option)',
        'Approach' => 'Text, (Choose one option)',
        'Sector' => 'Text, (Choose one option)',
        'Members Female 18-35' => 'Number (>=0)',
        'Members Male 18-35' => 'Number (>=0)',
        'Members Male 35+' => 'Number (>=0)',
        'Members Female 35+' => 'Number (>=0)',
        'Category' => 'Text, (Choose one option)',
        'Establishment Status' => 'New/Old, (Choose one option)',
        'Is Registered' => 'Boolean (1/0)',
        'Registration Body' => 'Text',
        'Registration Number' => 'Text',
        'Registration Date' => 'Date (dd-mm-yyyy)',
        'Employees Formal Female 18-35' => 'Number (>=0)',
        'Employees Formal Male 18-35' => 'Number (>=0)',
        'Employees Formal Male 35+' => 'Number (>=0)',
        'Employees Formal Female 35+' => 'Number (>=0)',
        'Employees Informal Female 18-35' => 'Number (>=0)',
        'Employees Informal Male 18-35' => 'Number (>=0)',
        'Employees Informal Male 35+' => 'Number (>=0)',
        'Employees Informal Female 35+' => 'Number (>=0)',
        'Area Under Cultivation' => 'Number (>=0)',
        'Is Registered Seed Producer' => 'Boolean (1/0)',
        'Seed Producer Registration Number' => 'Text',
        'Seed Producer Registration Date' => 'Date (dd-mm-yyyy)',
        'Uses Certified Seed' => 'Boolean (1/0)',

    ];

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function collection()
    {

        if ($this->template) {
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [

            array_keys($this->validationTypes),

            array_values($this->validationTypes)
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++; // Increment ID for each row

        return array_merge(
            [$this->rowNumber], // Add ID column value
            array_values($row->toArray()) // Map remaining row values
        );
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

                    'Collective production only',
                    'Collective marketing only',
                    'Knowledge Sharing only',
                    'Collective producing; marketing and knowledge sharing',
                    'NA'

                ];

                $this->setDataValidations($dropdownOptions, 'L3', $sheet);
                $dropdownOptions = [
                    'Farmers',
                    'Processors',
                    'Traders',
                    'Aggregators',
                    'Transporters'
                ];

                $this->setDataValidations($dropdownOptions, 'J3', $sheet);

                // Define the dropdown options
                $dropdownOptions = [

                    'Producer organization (PO)',
                    'Large scale farm',
                    'Large scale processor',
                    'Small medium enterprise (SME)',
                    'Other'


                ]; // Includes an empty option

                //Group
                $this->setDataValidations($dropdownOptions, 'K3', $sheet);



                $dropdownOptions = [
                    'Private',
                    'Public'
                ];

                //Sector
                $this->setDataValidations($dropdownOptions, 'M3', $sheet);

                $dropdownOptions = [
                    'Early generation seed producer',
                    'Seed multiplier',
                    'RTC producer'

                ];

                //Category
                $this->setDataValidations($dropdownOptions, 'R3', $sheet);


                $dropdownOptions = [
                    'New',
                    'Old'
                ];

                $this->setDataValidations($dropdownOptions, 'S3', $sheet);

                $dropdownOptions = [

                    'Potato',
                    'Sweet potato',
                    'Cassava'
                ];

                //Enterprise
                $this->setDataValidations($dropdownOptions, 'E3', $sheet);
            },
        ];
    }


    public function title(): string
    {
        return 'RTC Actor Recruitment';
    }
}