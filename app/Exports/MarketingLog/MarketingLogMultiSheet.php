<?php
namespace App\Exports\MarketingLog;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class MarketingLogMultiSheet implements WithMultipleSheets, WithStrictNullComparison
{

    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'RTC-Market Data' => new MarketingLogExport($this->template),
        ];
    }
}
