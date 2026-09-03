<?php
namespace App\Traits;

use App\Exceptions\ExcelValidationException;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Lightweight filter to read only the header row (Row 1)
 */
class HeaderOnlyReadFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row === 1;
    }
}

trait ChecksBlankSheets
{
    /**
     * Fast + low-memory check for:
     * 1. Required sheet existence
     * 2. Unexpected sheets
     * 3. Sheet header correctness
     * 4. Blank sheets (data row counts)
     */
    protected function assertBlankSheetRules(
        array $rowCounts,
        array $required,                // ['SheetName' => headerCount, ...]
        array $optional = [],           // ['SheetName' => headerCount, ...]
        ?array $expectedHeaders = null, // ['SheetName' => ['Col1', 'Col2'], ...]
        ?string $allBlankMessage = null,
        ?string $requiredBlankMessage = null
    ): void {
        // Fetch sheet names once
        $sheetNames = SheetNamesValidator::getSheetNames($this->filePath);

        // O(1) membership sets
        $presentSet  = array_flip($sheetNames);
        $expectedSet = isset($this->expectedSheetNames) ? array_flip($this->expectedSheetNames) : [];
        $ignoredSet  = isset($this->ignoredSheetNames) ? array_flip($this->ignoredSheetNames) : [];

        // 1) Required sheets must exist
        foreach ($required as $sheetName => $_headers) {
            if (! isset($presentSet[$sheetName])) {
                Log::error("Missing required sheet: {$sheetName}");
                throw new ExcelValidationException("The sheet '{$sheetName}' is missing.");
            }
        }

        // 2) No unexpected sheets
        foreach ($sheetNames as $name) {
            if (! isset($expectedSet[$name]) && ! isset($ignoredSet[$name])) {
                Log::error("Unexpected sheet name: {$name}");
                throw new ExcelValidationException("Unexpected sheet: '{$name}' in file.");
            }
        }

        // 3) Validate Sheet Headers (if expectedHeaders are supplied)
        if (! empty($expectedHeaders)) {
            $this->validateFileHeaders($expectedHeaders);
        }

        // Helper: compute data rows excluding header rows
        $getDataRows = function (string $sheetName, int $headerRows) use ($rowCounts): int {
            $total = (int) ($rowCounts[$sheetName] ?? 0);
            $data  = $total - $headerRows;
            return $data > 0 ? $data : 0;
        };

        // 4) If ALL sheets are blank -> throw exception
        $anyHasData = false;

        foreach ($required as $sheetName => $headers) {
            if ($getDataRows($sheetName, (int) $headers) > 0) {
                $anyHasData = true;
                break;
            }
        }

        if (! $anyHasData) {
            foreach ($optional as $sheetName => $headers) {
                if ($getDataRows($sheetName, (int) $headers) > 0) {
                    $anyHasData = true;
                    break;
                }
            }
        }

        if (! $anyHasData) {
            $allBlankMessage ??= 'All sheets are blank. Please ensure your file contains data before importing.';
            Log::error('All sheets are blank (required + optional).');
            throw new ExcelValidationException($allBlankMessage);
        }

        // 5) If ANY required sheet is blank -> throw exception
        $blankRequired = null;

        foreach ($required as $sheetName => $headers) {
            if ($getDataRows($sheetName, (int) $headers) === 0) {
                $blankRequired ??= [];
                $blankRequired[] = $sheetName;
            }
        }

        if (! empty($blankRequired)) {
            $requiredBlankMessage ??= 'Some required sheets are blank: ' . implode(', ', $blankRequired) . '.';
            Log::error('Required sheet(s) blank.', ['blankRequired' => $blankRequired]);
            throw new ExcelValidationException($requiredBlankMessage);
        }
    }

    /**
     * Efficiently reads row 1 of each expected sheet and compares headers.
     */
    protected function validateFileHeaders(array $expectedHeaders): void
    {
        $inputFileType = IOFactory::identify($this->filePath);
        $reader        = IOFactory::createReader($inputFileType);

        // Read only row 1 into memory
        $reader->setReadFilter(new HeaderOnlyReadFilter());
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($this->filePath);

        foreach ($expectedHeaders as $sheetName => $expectedCols) {
            // Skip header check if the sheet isn't present in the document
            if (! $spreadsheet->sheetNameExists($sheetName)) {
                continue;
            }

            $worksheet     = $spreadsheet->getSheetByName($sheetName);
            $highestColumn = $worksheet->getHighestColumn();

            // Extract row 1 values
            $headerCells = $worksheet->rangeToArray("A1:{$highestColumn}1", null, true, false);
            $actualCols  = $headerCells[0] ?? [];

            // Trim whitespace for reliable comparison
            $cleanActual   = array_values(array_map('trim', array_filter($actualCols, fn($v) => ! is_null($v) && $v !== '')));
            $cleanExpected = array_values(array_map('trim', $expectedCols));

            if ($cleanActual !== $cleanExpected) {
                Log::error("Header mismatch in sheet '{$sheetName}'", [
                    'expected' => $cleanExpected,
                    'actual'   => $cleanActual,
                ]);

                throw new ExcelValidationException(
                    "Invalid template headers in sheet '{$sheetName}'. Please ensure you are using the correct file format."
                );
            }
        }

        // Free memory immediately
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}
