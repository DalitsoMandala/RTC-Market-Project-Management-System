<?php

namespace App\Exports\RtcRecruitment;

use App\Models\Recruitment;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
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
    use FormEssentials;



    protected $rowNumber = 0; // Start counting from 1
    public $template;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Recruitment Form']['RTC Actor Recruitment'];
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
