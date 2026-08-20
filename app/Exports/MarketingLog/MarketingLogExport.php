<?php
namespace App\Exports\MarketingLog;

use App\Models\CropVariety;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class MarketingLogExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;
    use FormEssentials;

    protected $rowNumber = 0; // Start counting from 1
    public bool $template;
    protected $validationTypes = [];

    public function __construct(bool $template)
    {
        $this->template        = $template;
        $this->validationTypes = $this->forms['Production and Marketing Log']['RTC-Market Data'];
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
            array_values($this->validationTypes),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet         = $event->sheet->getDelegate();
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
                        'bold'  => true,
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFC5'], // Pink background
                    ],

                ]);

                $sheet = $event->sheet->getDelegate();

                $dropdownOptions = [

                    'Potato',
                    'Sweet potato',
                    'Cassava',
                ];
                $this->setDataValidations($dropdownOptions, 'E3', $sheet);

                $dropdownOptions = [
                    'Table Potato', 'Seed',

                ]; // Includes an empty option

                //Group
                $this->setDataValidations($dropdownOptions, 'F3', $sheet);

                $dropdownOptions = [
                    "Rainfed", 'Winter',

                ];

                //Category
                $this->setDataValidations($dropdownOptions, 'G3', $sheet);

                $dropdownOptions = [
                    'Male',
                    'Female',
                ];

                //Sector
                $this->setDataValidations($dropdownOptions, 'L3', $sheet);

                $dropdownOptions = CropVariety::all()->pluck('name')->toArray();
                //Type
                $this->setDataValidations($dropdownOptions, 'O3', $sheet);
            },
        ];
    }

    public function title(): string
    {
        return 'RTC-Market Data';
    }
}
