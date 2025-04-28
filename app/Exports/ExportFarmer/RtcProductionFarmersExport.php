<?php

namespace App\Exports\ExportFarmer;

use Carbon\Carbon;
use App\Models\RtcProductionFarmer;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionFarmersExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{



    use ExportStylingTrait;
    use FormEssentials;



    protected $rowNumber = 0; // Start counting from 1
    public $template;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Farmers Form']['Production Farmers'];
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

                    'Potato',
                    'Sweet potato',
                    'Cassava'
                ];
                $this->setDataValidations($dropdownOptions, 'G3', $sheet);
            },
        ];
    }


    public function title(): string
    {
        return 'Production Farmers';
    }
}
