<?php

namespace App\Exports\RootTuberExport;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ProcessedExportSheet implements FromCollection,WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use \App\Traits\ExportStylingTrait;
    use \App\Traits\FormEssentials;
    public $template = false;
    protected $validationTypes = [];
    public function __construct($template = false)
    {

        $this->template = $template;
        $this->validationTypes = $this->forms['Root and Tuber Exports Form']['ROOTS & TUBER PROCESSED EXPORTS'];
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
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
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
        return 'ROOTS & TUBER PROCESSED EXPORTS';
    }
}
