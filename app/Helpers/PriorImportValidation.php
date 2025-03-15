<?php

namespace App\Helpers;

use Psr\Log\LoggerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exceptions\ExcelValidationException;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class PriorImportValidation
{
    private $filePath;
    private $expectedSheetNames;
    private $expectedHeaders;
    private $logger;
    private $event;

    public function __construct($filePath, array $expectedSheetNames, array $expectedHeaders, $event)
    {
        $this->filePath = $filePath;
        $this->expectedSheetNames = $expectedSheetNames;
        $this->expectedHeaders = $expectedHeaders;
        $this->event = $event;
    }

    public function validate()
    {
        $reader = IOFactory::createReaderForFile($this->filePath);
        $spreadsheet = $reader->load($this->filePath);
        $sheetNames = $spreadsheet->getSheetNames();

        $this->validateSheetNames($sheetNames);
        $this->validateSheetContent($spreadsheet);
        $this->validateHeaders($spreadsheet, $sheetNames);
    }

    private function validateSheetNames(array $sheetNames)
    {
        $firstSheetName = $this->expectedSheetNames[0];

        foreach ($sheetNames as $sheetName) {
            if ($sheetName === $firstSheetName && $this->isSheetBlank($sheetName)) {
                Log::error("The sheet '{$firstSheetName}' is blank.");
                throw new ExcelValidationException("The sheet '{$firstSheetName}' is blank. Please ensure it contains data before importing.");
            }
        }
    }

    private function validateSheetContent($spreadsheet)
    {
        $workBook = $this->event->reader->getTotalRows();

        foreach ($workBook as $sheetName => $totalRows) {
            if ($this->isSheetBlank($sheetName)) {
                // You can add additional logic here if needed
            }
        }
    }

    private function validateHeaders($spreadsheet, $sheetNames)
    {
        foreach ($this->expectedHeaders as $sheetName => $expectedHeaders) {
            // Check if sheet exists

            if (!in_array($sheetName, $sheetNames)) {
                throw new ExcelValidationException("Sheet '{$sheetName}' is missing in the uploaded file.");
            }

            // Get the sheet by name
            $sheet = $spreadsheet->getSheetByName($sheetName);

            // Validate headers
            $actualHeaders = $this->getSheetHeaders($sheet);
            if (!$this->validateHeaders($actualHeaders, $expectedHeaders)) {
                throw new ExcelValidationException("Headers in sheet '{$sheetName}' do not match the expected format.");
            }
        }
    }

    private function isSheetBlank(string $sheetName)
    {
        $reader = IOFactory::createReaderForFile($this->filePath);
        $spreadsheet = $reader->load($this->filePath);
        $workBook = $this->event->reader->getTotalRows();

        return $workBook[$sheetName] <= 2;
    }

    private function getSheetHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // You need to implement this method to get the headers from the sheet
    }
}
