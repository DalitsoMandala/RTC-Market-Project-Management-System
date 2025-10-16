<?php

namespace App\Exports\Reports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
                'Y1 Achieved',
                'Y2 target',
                'Y2 Achieved',
                'Y2 % Achieved',
                'Y3 target',
                'Y3 Achieved',
                'Y3 % Achieved',
                'Y4 target',
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
        $sheet->mergeCells('E1:M1');
        $sheet->mergeCells('N1:N2');
        $sheet->mergeCells('O1:O2');
        $sheet->mergeCells('P1:P2');
        $sheet->mergeCells('Q1:Q2');

        // Center align headers
        $sheet->getStyle('A1:Q2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:Q2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:Q2')->getFont()->setBold(true);

    // Header fill colors
    $sheet->getStyle('A1:Q1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD966'); // Gold
    $sheet->getStyle('A2:Q2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC'); // Light yellow



        // Add borders
        $sheet->getStyle('A1:Q300')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}
