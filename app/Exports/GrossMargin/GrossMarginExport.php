<?php

namespace App\Exports\GrossMargin;

use App\Models\GrossMarginCategory;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GrossMarginExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents, ShouldAutoSize
{

    public function __construct() {}

    public function seeds()
    {
        $arr = [];
        for ($i = 0; $i < 5; $i++) {
            array_push($arr, [' ', 'Bundle/Kg', '', '', '-']);
        }
        return $arr;
    }

    public function categoryItems($number)
    {
        $category = GrossMarginCategory::with('categoryItems')->find($number);

        if (!$category) {
            return []; // Return empty array if not found
        }
        return [
            'name' => $category->name,
            'id' => $category->id,
            'items' => $category->categoryItems->map(function ($item) {
                return [$item->item_name, $item->unit, '', '', '-'];
            })->toArray(),

        ];


        return $arr;
    }
    public function array(): array
    {
        return [
            ['Gross Margin Form'],
            ['Name of Producer:',  'Sex:', 'Phone number:',  'Date:'],
            ['District:', 'T/A',  'Village:',],
            ['EPA:',  'Section:',  'GPS-  S:',  'E:',  'Elevation:'],
            ['Crop: Cassava/Potato/Sweetpotato', 'Type of produce: Seed/Ware/Cuttings', 'Season: Rainfed/Irrigated'],

            ['Item', 'Unit (Mulingo)', 'QTY (Kuchuluka)', 'Unit price (Mtengo wa chimodzi)', 'Total (Zonse pamodzi)'],
            [GrossMarginCategory::find(1)->name ?? ''], // title
            $this->seeds(),
            [GrossMarginCategory::find(2)->name ?? ''],
            $this->categoryItems(2)['items'],
            [GrossMarginCategory::find(3)->name ?? ''], // title
            $this->categoryItems(3)['items'],
            [GrossMarginCategory::find(4)->name ?? ''], // title
            $this->categoryItems(4)['items'],
            [GrossMarginCategory::find(5)->name ?? ''], // title
            $this->categoryItems(5)['items'],
            ['Total valuable cost (Zolowa zonse)', '', '', '', '-'],
            ['Total harvest (Zokolora)', 'kg/bundle', '', ''],
            ['Prevailing Selling price (Mtengo ogulitsira wapamsika)', 'kg/bundle', '1', '', '-'],
            ['Income (Zogulitsa)', 'kg/bundle', '', '', '-'],
            ['Yield', '', '', '', '-'],
            ['Break even Yield', '', '', '', '-'],
            ['Break even Price', '', '', '', '-'],
            ['Gross Margin (Profit)', '', '', '', '-'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]], // Main Title
            7    => ['font' => ['bold' => true, 'size' => 12,],],
            13   => ['font' => ['bold' => true, 'size' => 12,],],
            18   => ['font' => ['bold' => true, 'size' => 12,],],
            27   => ['font' => ['bold' => true, 'size' => 12,],],
            34   => ['font' => ['bold' => true, 'size' => 12,],],
            40   => ['font' => ['bold' => true, 'size' => 12,],],
            41   => ['font' => ['bold' => true, 'size' => 12,],],
            42   => ['font' => ['bold' => true, 'size' => 12,],],
            43   => ['font' => ['bold' => true, 'size' => 12,],],
            44   => ['font' => ['bold' => true, 'size' => 12,],],
            45   => ['font' => ['bold' => true, 'size' => 12,],],
            46   => ['font' => ['bold' => true, 'size' => 12,],],
            47   => ['font' => ['bold' => true, 'size' => 12,],],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45, // Item
            'B' => 20, // Unit
            'C' => 18, // QTY
            'D' => 25, // Unit Price
            'E' => 20, // Total
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge top title row across columns A–E
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'FCE4D6'],
                    ]
                ])->getFont()->setBold(true)->setSize(14)->setName('Bahnschrift Condensed');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Header row (row 7) styling
                $sheet->getStyle('A6:E6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                        'size' => 12,
                        'name' => 'Bahnschrift Condensed',
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'FCE4D6'], // deep blue header background
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                $sheet->getStyle('A1:E47')->applyFromArray([
                    'font' => [

                        'name' => 'Bahnschrift Condensed',
                    ],
                ]);
                // // Apply borders for the entire used range
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A6:E{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                $sheet->getStyle('A7:E7')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                        'endColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ]
                ]);

                $sheet->getStyle('A13:E13')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                        'endColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ]
                ]);

                $sheet->getStyle('A18:E18')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                        'endColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ]
                ]);


                $sheet->getStyle('A27:E27')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                        'endColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ]
                ]);

                $sheet->getStyle('A34:E34')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                        'endColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ]
                ]);

                $sheet->getStyle('A40:E40')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'F2F2F2'],
                        'endColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ]
                ]);

                $sheet->getStyle('D41:E41')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'AEAAAA'],
                        'endColor' => [
                            'rgb' => 'AEAAAA',
                        ],
                    ]
                ]);

                $sheet->getStyle('A44:E47')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'D6DCE4'],
                        'endColor' => [
                            'rgb' => 'D6DCE4',
                        ],
                    ]
                ]);
            }
        ];
    }
    public function title(): string
    {
        return 'Gross Margin Form';
    }
}
