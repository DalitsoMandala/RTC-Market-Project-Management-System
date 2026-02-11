<?php

namespace App\Traits;

use App\Exceptions\ExcelValidationException;
use App\Helpers\SheetNamesValidator;
use Illuminate\Support\Facades\Log;

trait ChecksBlankSheets
{
    /**
     * Fast + low-memory:
     * - Fetch sheet names ONCE
     * - Use hash-sets (array_flip) for O(1) checks
     * - Single-pass checks where possible
     */
    protected function assertBlankSheetRules(
        array $rowCounts,
        array $required,                      // ['Names' => 2, ...]
        array $optional = [],                 // ['Other information' => 2, ...]
        ?string $allBlankMessage = null,
        ?string $requiredBlankMessage = null
    ): void {
        // Fetch once (avoid calling this inside a loop)
        $sheetNames = SheetNamesValidator::getSheetNames($this->filePath);

        // O(1) membership tests
        $presentSet  = array_flip($sheetNames);
        $expectedSet = isset($this->expectedSheetNames) ? array_flip($this->expectedSheetNames) : [];
        $ignoredSet  = isset($this->ignoredSheetNames) ? array_flip($this->ignoredSheetNames) : [];

        // 1) Required sheets must exist (FAST)
        foreach ($required as $sheetName => $_headers) {
            if (!isset($presentSet[$sheetName])) {
                Log::error("Missing required sheet: {$sheetName}");
                throw new ExcelValidationException("The sheet '{$sheetName}' is missing.");
            }
        }

        // 2) No unexpected sheets (allow expected + ignored)
        // If you want to allow ONLY expected sheets + ignored sheets, keep this block.
        // If you want to allow other random sheets, remove this block entirely.
        foreach ($sheetNames as $name) {
            if (!isset($expectedSet[$name]) && !isset($ignoredSet[$name])) {
                Log::error("Unexpected sheet name: {$name}");
                throw new ExcelValidationException("Unexpected sheet: '{$name}' in file.");
            }
        }

        // Helper: compute "data rows" (excluding header rows) with minimal overhead
        // (avoid closures in tight loops if you want micro-optimizations)
        $getDataRows = function (string $sheetName, int $headerRows) use ($rowCounts): int {
            $total = (int) ($rowCounts[$sheetName] ?? 0);
            $data  = $total - $headerRows;
            return $data > 0 ? $data : 0;
        };

        // 3) If ALL (required + optional) are blank -> throw
        // Single pass over combined list, without building big log arrays
        $anyHasData = false;

        foreach ($required as $sheetName => $headers) {
            if ($getDataRows($sheetName, (int) $headers) > 0) {
                $anyHasData = true;
                break;
            }
        }

        if (!$anyHasData) {
            foreach ($optional as $sheetName => $headers) {
                if ($getDataRows($sheetName, (int) $headers) > 0) {
                    $anyHasData = true;
                    break;
                }
            }
        }

        if (!$anyHasData) {
            $allBlankMessage ??= 'All sheets are blank. Please ensure your file contains data before importing.';
            Log::error('All sheets are blank (required + optional).');
            throw new ExcelValidationException($allBlankMessage);
        }

        // 4) If ANY required sheet is blank -> throw
        // Build list only if needed (saves memory)
        $blankRequired = null; // lazy init

        foreach ($required as $sheetName => $headers) {
            if ($getDataRows($sheetName, (int) $headers) === 0) {
                $blankRequired ??= [];
                $blankRequired[] = $sheetName;
            }
        }

        if (!empty($blankRequired)) {
            $requiredBlankMessage ??= 'Some required sheets are blank: ' . implode(', ', $blankRequired) . '.';
            Log::error('Required sheet(s) blank.', ['blankRequired' => $blankRequired]);
            throw new ExcelValidationException($requiredBlankMessage);
        }
    }
}
