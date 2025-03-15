<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait excelDateFormat
{
    //

    public function convertExcelDate($row)
    {
        $data = null;

        // Check if the row is a numeric Excel date (serial number)
        if (is_numeric($row)) {
            try {
                // Convert Excel serial date to a DateTime object and format it
                $row = Carbon::instance(Date::excelToDateTimeObject($row))->format('d-m-Y');
            } catch (\Exception $e) {
                // Log error if the conversion fails
                Log::error("Invalid Excel date format: " . $e->getMessage());
                $row = Carbon::now()->format('d-m-Y'); // Default to current date if invalid
            }
        }
        // Check if the row is a string with a specific date format
        elseif (is_string($row)) {
            try {
                // Try to parse the date from the string (assuming d-m-Y format)
                $row = Carbon::createFromFormat('d-m-Y', $row)->format('d-m-Y');
            } catch (\Exception $e) {
                // Log error if the format is incorrect
                Log::error("Invalid date format: " . $row);
                $row = Carbon::now()->format('d-m-Y'); // Default to current date if invalid
            }
        }
        // If the row is empty or of an unexpected type, set a default value
        else {
            $row = Carbon::now()->format('d-m-Y'); // Default to current date if empty or invalid
        }

        $data = $row;
        return $data;
    }
}