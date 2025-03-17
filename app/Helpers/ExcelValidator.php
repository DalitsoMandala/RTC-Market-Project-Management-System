<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
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
        $reader = $this->createReader();
        $reader->setReadDataOnly(true); // Only read data, not formatting
        $reader->setReadEmptyCells(false); // Skip empty cells

        try {
            $spreadsheet = $reader->load($this->filePath);
        } catch (\Exception $e) {
            Log::error("Failed to load Excel file: " . $e->getMessage());
            return new ExcelValidationException("Failed to load the Excel file. Please ensure the file is valid.");
        }

        $sheetNames = $spreadsheet->getSheetNames();

        foreach ($this->expectedSheetNames as $sheetName) {
            $sheetIndex = array_search($sheetName, $sheetNames);

            if ($sheetIndex === false) {
                Log::error("Missing expected sheet: {$sheetName}");
                return new ExcelValidationException("The sheet '{$sheetName}' is missing. Please ensure the file contains all required sheets.");
            }

            $sheet = $spreadsheet->getSheet($sheetIndex);

            // Retrieve the first row as headers
            $headers = $this->getHeadersFromSheet($sheet);

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
        }

        // No exceptions, return null if everything is valid
        return null;
    }

    /**
     * Create a reader instance optimized for large files.
     *
     * @return IReader
     */
    protected function createReader()
    {
        $reader = IOFactory::createReaderForFile($this->filePath);

        if (method_exists($reader, 'setReadFilter')) {
            $reader->setReadFilter(new ChunkReadFilter());
        }

        return $reader;
    }

    /**
     * Extract headers from the sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return array
     */
    protected function getHeadersFromSheet($sheet)
    {
        $headers = [];
        $highestColumn = $sheet->getHighestColumn();
        $headerRow = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0];

        foreach ($headerRow as $header) {
            if (!empty($header)) {
                $headers[] = $header;
            }
        }

        return $headers;
    }
}

/**
 * Read filter class for reading only the first row (headers).
 */
class ChunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        // Only read the first row (headers)
        return $row == 1;
    }
}