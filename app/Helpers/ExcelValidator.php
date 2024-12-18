<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ExcelValidationException;


class ExcelValidator
{
    protected $filePath;
    protected $expectedSheetNames;
    protected $expectedHeaders;

    public function __construct($filePath, array $expectedSheetNames, array $expectedHeaders)
    {
        $this->filePath = $filePath;
        $this->expectedSheetNames = $expectedSheetNames;
        $this->expectedHeaders = $expectedHeaders;
    }

    /**
     * Validates the Excel file headers and returns any validation errors.
     *
     * @return ExcelValidationException|null
     */
    public function validateHeaders()
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheetNames = $spreadsheet->getSheetNames();

        foreach ($this->expectedSheetNames as $sheetName) {
            $sheetIndex = array_search($sheetName, $sheetNames);

            if ($sheetIndex === false) {
                Log::error("Missing expected sheet: {$sheetName}");
                return new ExcelValidationException("The sheet '{$sheetName}' is missing. Please ensure the file contains all required sheets.");
            }

            $sheet = $spreadsheet->getSheet($sheetIndex);

            // Retrieve the first row as headers
            $headers = [];
            $highestColumn = $sheet->getHighestColumn();
            $headerRow = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0];

            foreach ($headerRow as $header) {
                $headers[] = $header;
            }

            // Validate headers
            $expectedSheetHeaders = $this->expectedHeaders[$sheetName];
            $missingHeaders = array_diff($expectedSheetHeaders, $headers);
            $extraHeaders = array_diff($headers, $expectedSheetHeaders);

            if (!empty($missingHeaders) || !empty($extraHeaders)) {
                $missing = implode(', ', $missingHeaders);
                $extra = implode(', ', $extraHeaders);
                Log::error("Invalid Columns in sheet: {$sheetName}");
                return new ExcelValidationException(
                    "Invalid Columns in sheet '{$sheetName}'. Missing Columns: {$missing}. Unknown Columns: {$extra}."
                );
            }

            // Check if the sheet is blank
            // $totalRows = $sheet->getHighestRow();
            // if ($totalRows <= 1) { // Only header row present or blank
            //     Log::error("The sheet '{$sheetName}' is blank.");
            //     return new ExcelValidationException("The sheet '{$sheetName}' is blank. Please ensure it contains data before importing.");
            // }
        }

        // No exceptions, return null if everything is valid
        return null;
    }
}