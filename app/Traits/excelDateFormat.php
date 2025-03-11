<?php

namespace App\Traits;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait excelDateFormat
{
    //

    public function convertExcelDate($row)
    {

        $data = null;
        if (is_numeric($row)) {
            // Convert Excel serial date to d-m-Y before validation
            $row = Carbon::instance(Date::excelToDateTimeObject($row))->format('d-m-Y');
        } elseif (is_string($row)) {

            $row = Carbon::createFromFormat('d-m-Y', $row)->format('d-m-Y');
        } else {
            // Set default value if the date is empty
            $row = Carbon::now()->format('d-m-Y');
        }
        $data = $row;
        return $data;
    }
}