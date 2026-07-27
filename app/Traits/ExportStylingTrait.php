<?php
namespace App\Traits;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait ExportStylingTrait
{
    public function styles($sheet, $highestColumn)
    {}

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
            },
        ];
    }

    public function setDataValidations($options, $cell, $sheet, $listName = null)
    {
        $spreadsheet = $sheet->getParent();

        // 1. If listName is null, generate a random alphanumeric name
        // Use bin2hex(random_bytes) for a secure, random 8-character string
        if (is_null($listName)) {
            $listName = 'List_' . bin2hex(random_bytes(4));
        }

        // 2. Access or create the hidden "Lists" sheet
        $listSheetName = 'ValidationLists';
        $listSheet     = $spreadsheet->getSheetByName($listSheetName);
        if (! $listSheet) {
            $listSheet = $spreadsheet->createSheet();
            $listSheet->setTitle($listSheetName);
            $listSheet->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
        }

        // 3. Find the next empty column in the hidden sheet to avoid overwriting
        // stringFromColumnIndex starts at 1
        $nextColumnIndex = $listSheet->getHighestColumn() === 'A' && $listSheet->getCell('A1')->getValue() == ''
            ? 1
            : Coordinate::columnIndexFromString($listSheet->getHighestColumn()) + 1;

        $columnLetter = Coordinate::stringFromColumnIndex($nextColumnIndex);

        // 4. Populate the hidden column with your options
        foreach ($options as $index => $option) {
            $listSheet->setCellValue($columnLetter . ($index + 1), $option);
        }

        // 5. Define the Validation formula (range reference)
        $endOptionRow = count($options);
        $formula      = "{$listSheetName}!\${$columnLetter}\$1:\${$columnLetter}\${$endOptionRow}";

        // 6. Create and configure the validation object
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowErrorMessage(true)
            ->setShowDropDown(true)
            ->setFormula1($formula);

        // 7. Apply to the target range (M3:M10000) using setSqref (efficient)
        $cellLetter = preg_replace('/[0-9]/', '', $cell);
        $startRow   = (int) filter_var($cell, FILTER_SANITIZE_NUMBER_INT);
        $endRow     = 10000;

        $range = "{$cellLetter}{$startRow}:{$cellLetter}{$endRow}";
        $validation->setSqref($range);
        $sheet->setDataValidation($range, $validation);
    }
}
