<?php

namespace App\Traits;

use App\Exceptions\ExcelValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait excelDateFormat
{
    //

    public function convertExcelDate($value, $row = [])
    {
        try {
            if (is_numeric($value)) {
                // Convert Excel serial date to Y-m-d
                return Carbon::instance(Date::excelToDateTimeObject($value))->format('d-m-Y');
            }
        } catch (\Exception $e) {
            Log::error('Excel Time Object Error ' . $e->getMessage() . implode(', ', $row));
            //throw new ExcelValidationException('Invalid date format: ' . json_encode($value));
            return null;
        }

        try {
            if (is_string($value)) {
                // Convert d-m-Y string date to Y-m-d
                return Carbon::createFromFormat('d-m-Y', $value)->format('d-m-Y');
            }
        } catch (\Exception $e) {
            Log::error('CreateFromFormat Error ' . $e->getMessage() . implode(', ', $row));
            //  throw new ExcelValidationException('Invalid date format: ' . json_encode($value));
            return null;
        }


        Log::error('Error conversion' . implode(', ', $row));
        // throw new ExcelValidationException('Invalid date format: ' . json_encode($value));

        // Default to current date if conversion fails
        return null;
    }
}
