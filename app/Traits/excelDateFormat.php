<?php

namespace App\Traits;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait excelDateFormat
{
    //

    public function convertExcelDate($row)
    {
        if (!empty($row) && is_numeric($row)) {
            // Convert Excel serial date to d-m-Y before validation
            $row = Carbon::instance(Date::excelToDateTimeObject($row))->format('d-m-Y');
        } elseif (!empty($row)) {
            try {
                $row = Carbon::createFromFormat('d-m-Y', $row)->format('d-m-Y');
            } catch (\Exception $e) {
                \Log::error('Date conversion failed', ['date' => $row, 'error' => $e->getMessage()]);
                // Set default value if the date is invalid
                $row = Carbon::now()->format('d-m-Y');
            }
        } else {
            // Set default value if the date is empty
            $row = Carbon::now()->format('d-m-Y');
        }

        return $row;
    }
}
