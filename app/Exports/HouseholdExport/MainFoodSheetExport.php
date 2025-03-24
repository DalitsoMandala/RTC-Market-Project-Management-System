<?php

namespace App\Exports\HouseholdExport;

use App\Models\MainFoodHrc;
use App\Traits\ExportStylingTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class MainFoodSheetExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;
    public $template;
    protected $validationTypes = [
        'Household ID' => '(Number), Exists in Household Sheet',
        'Main Food Name' => 'Text, (Cassava, Sweet Potato, Potato)',
    ];

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function headings(): array
    {
        return [
            [
                'Household ID',
                'Main Food Name',
            ],
            array_values($this->validationTypes)
        ];
    }

    public function collection(): Collection
    {
        if ($this->template) {
            return collect([]);
        }
        return MainFoodHrc::select([
            'hrc_id',
            'name',
        ])->get();
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

                    'Cassava',
                    'Sweet potato',
                    'Potato',

                ]; // Includes an empty option

                $this->setDataValidations($dropdownOptions, 'B3', $sheet);
            },
        ];
    }

    public function title(): string
    {
        return 'Main Food Data';
    }
}
