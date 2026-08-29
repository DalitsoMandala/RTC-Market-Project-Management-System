<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

trait ExcelDateFormat
{
    /**
     * Formats accepted when parsing string dates, tried in order.
     */
    protected array $excelDateInputFormats = [
        'd-m-Y',
        'd/m/Y',
        'Y-m-d',
        'd F Y',
        'd M Y',
    ];

    /**
     * Output format for converted dates.
     */
    protected string $excelDateOutputFormat = 'd-m-Y';

    /**
     * Convert a raw Excel cell value into a normalized date string.
     *
     * @return string|null Returns null if the value is empty or cannot be parsed.
     */
    public function convertExcelDate(mixed $value, array $row = []): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format($this->excelDateOutputFormat);
            }

            if (is_numeric($value)) {
                return Carbon::instance(
                    Date::excelToDateTimeObject((float) $value)
                )->format($this->excelDateOutputFormat);
            }

            if (is_string($value)) {
                return $this->parseDateString(trim($value));
            }
        } catch (Throwable $e) {
            Log::error('Excel date conversion failed', [
                'value' => $value,
                'row'   => $row,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Attempt to parse a string date against known formats, falling back
     * to Carbon's flexible parser.
     */
    protected function parseDateString(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        foreach ($this->excelDateInputFormats as $format) {
            $date = Carbon::createFromFormat($format, $value);

            if ($date !== false && $date->format($format) === $value) {
                return $date->format($this->excelDateOutputFormat);
            }
        }

        // Fallback: flexible parser (throws on genuinely invalid input,
        // which is caught by the caller's try/catch)
        return Carbon::parse($value)->format($this->excelDateOutputFormat);
    }
}
