<?php

namespace App\Exports\Reports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ReportExportTemplate
{
    public function registerEvents(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            [
                'Indicator No.',
                'Indicator',
                'Baseline value',
                'Disaggregation',
                'Annual performance reporting',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'LOP targets',
                'Achievement to date',
                '% Achieved to date',
                'Comments'
            ],
            [
                '',
                '',
                '',
                '',
                'Y1 Target',
                'Y1 Achieved',
                'Y1 % Achieved',
                'Y2 Target',
                'Y2 Achieved',
                'Y2 % Achieved',
                'Y3 Target',
                'Y3 Achieved',
                'Y3 % Achieved',
                'Y4 Target',
                'Y4 Achieved',
                'Y4 % Achieved',
                '',
                '',
                '',
                ''
            ],
        ];
    }


    public function styles(Worksheet $sheet)
    {
        // Merge header cells
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');
        $sheet->mergeCells('E1:P1');
        $sheet->mergeCells('Q1:Q2');
        $sheet->mergeCells('R1:R2');
        $sheet->mergeCells('S1:S2');
        $sheet->mergeCells('T1:T2');
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setWidth(40);
        }

        foreach (range('E', 'T') as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // 2️⃣ Enable wrap text for all used cells
        $sheet->getStyle(
            $sheet->calculateWorksheetDimension()
        )->getAlignment()->setWrapText(true);
        // Center align headers
        $sheet->getStyle('A1:T2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:T2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:T2')->getFont()->setBold(true);
        $sheet->getStyle('A1:T300')->applyFromArray([
            'font' => [

                'color' => ['rgb' => '000000'],
                'size' => 12,
                'name' => 'Bahnschrift Condensed',
            ],
        ]);
        // Header fill colors
        $sheet->getStyle('A1:T1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD966'); // Gold
        $sheet->getStyle('A2:T2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC'); // Light yellow

        //Number formats
        $sheet->getStyle('E:P')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        // Add borders
        $sheet->getStyle('A1:T300')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}
