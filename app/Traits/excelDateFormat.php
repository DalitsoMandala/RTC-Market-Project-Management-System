<?php

namespace App\Traits;

use App\Exceptions\ExcelValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait excelDateFormat
{


    public function convertExcelDate($value, $row = [])
    {
        try {

            // Excel numeric date
            if (is_numeric($value)) {
                return Carbon::instance(
                    Date::excelToDateTimeObject($value)
                )->format('d-m-Y');
            }

            // Already a DateTime/Carbon instance
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format('d-m-Y');
            }

            if (is_string($value)) {

                $value = trim($value);

                $formats = [
                    'd-m-Y',
                    'd/m/Y',
                    'Y-m-d',
                    'd F Y',
                    'd M Y'
                ];

                foreach ($formats as $format) {
                    try {
                        return Carbon::createFromFormat($format, $value)->format('d-m-Y');
                    } catch (\Exception $e) {
                        // try next format
                    }
                }

                // fallback flexible parser
                return Carbon::parse($value)->format('d-m-Y');
            }
        } catch (\Throwable $e) {

            Log::error('Excel Date Conversion Failed', [
                'value' => $value,
                'row' => $row,
                'error' => $e->getMessage()
            ]);
        }

        return 0;
    }
    //     public function convertExcelDate($value, $row = [])
    // {
    //     if (is_numeric($value)) {
    //         try {
    //             return Carbon::instance(Date::excelToDateTimeObject($value))->format('d-m-Y');
    //         } catch (\Exception $e) {
    //             Log::error('Excel Time Object Error ' . $e->getMessage() . implode(', ', $row));
    //             return 0;
    //         }
    //     }

    //     if (is_string($value)) {
    //         try {
    //             // Try your original format first
    //             return Carbon::createFromFormat('d-m-Y', $value)->format('d-m-Y');
    //         } catch (\Exception $e) {
    //             try {
    //                 // Then try the full month name format
    //                 return Carbon::createFromFormat('d F Y', $value)->format('d-m-Y');
    //             } catch (\Exception $e2) {
    //                 // Finally try Carbon's flexible parser
    //                 return Carbon::parse($value)->format('d-m-Y');
    //             }
    //         }
    //     }

    //     Log::error('Error conversion' . implode(', ', $row));
    //     return 0;
    // }
    //

    // public function convertExcelDate($value, $row = [])
    // {
    //     try {
    //         if (is_numeric($value)) {
    //             // Convert Excel serial date to Y-m-d
    //             return Carbon::instance(Date::excelToDateTimeObject($value))->format('d-m-Y');
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Excel Time Object Error ' . $e->getMessage() . implode(', ', $row));
    //         // throw new ExcelValidationException('Invalid date format: ' . json_encode($value));
    //         return 0;
    //     }

    //     try {
    //         if (is_string($value)) {
    //             // Convert d-m-Y string date to Y-m-d
    //             return Carbon::createFromFormat('d-m-Y', $value)->format('d-m-Y');
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('CreateFromFormat Error ' . $e->getMessage() . implode(', ', $row));
    //         //  throw new ExcelValidationException('Invalid date format: ' . json_encode($value));
    //         return 0;
    //     }

    //     Log::error('Error conversion' . implode(', ', $row));

    //     return 0;
    // }
}
