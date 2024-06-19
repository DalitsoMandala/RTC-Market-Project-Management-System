<?php
namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class SheetNamesValidator
{

    public static function getSheetNames($filepath): array
    {
        if (!file_exists($filepath)) {
            return [];
        }

        $reader = IOFactory::createReaderForFile($filepath);
        $spreadsheet = $reader->load($filepath);

        // Get the number of sheets
        $sheetNames = $spreadsheet->getSheetNames();
        return $sheetNames;
    }
}
