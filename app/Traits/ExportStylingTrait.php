<?php

namespace App\Traits;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

trait ExportStylingTrait
{
    //
    public function styles($sheet, $highestColumn) {}
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
            },
        ];
    }

    // public function setDataValidations($options, $cell, $sheet)
    // {



    //     $dropdownValues = '"' . implode(',', $options) . '"';

    //     $validation = $sheet->getCell($cell)->getDataValidation();
    //     $validation->setType(DataValidation::TYPE_LIST)
    //         ->setFormula1($dropdownValues)
    //         ->setAllowBlank(true)
    //         ->setShowInputMessage(true)
    //         ->setShowErrorMessage(true)
    //         ->setErrorStyle(DataValidation::STYLE_STOP)
    //         ->setShowDropDown(true);

    //     // Apply the same validation to the specified cell
    //     $cellLetter = substr($cell, 0, 1);
    //     $sheet->setDataValidation("{$cell}:{$cellLetter}1048576", $validation);
    // }

    public function setDataValidations($options, $cell, $sheet)
    {
        $dropdownValues = '"' . implode(',', $options) . '"';

        // Create the base validation object
        $baseValidation = new DataValidation();
        $baseValidation->setType(DataValidation::TYPE_LIST)
            ->setFormula1($dropdownValues)
            ->setAllowBlank(true)
            ->setShowInputMessage(true)
            ->setShowErrorMessage(true)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowDropDown(true);

        // Extract the column letter(s) and starting row number from the given cell
        $cellLetter = preg_replace('/[0-9]/', '', $cell);
        $startRow = (int) filter_var($cell, FILTER_SANITIZE_NUMBER_INT);

        // Define how many rows you want to apply validation to
        $endRow = 10000; // <-- You can change this number as needed

        // Apply validation individually to each cell in the column
        for ($row = $startRow; $row <= $endRow; $row++) {
            $targetCell = $cellLetter . $row;
            $validationClone = clone $baseValidation;
            $sheet->getCell($targetCell)->setDataValidation($validationClone);
        }
    }
}