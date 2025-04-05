<?php

namespace App\Exports\RtcRecruitment;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcRecruitmentMultiSheetExport implements WithMultipleSheets, WithStrictNullComparison
{

    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'RTC Actor Recruitment' => new RtcRecruitmentExport($this->template),
            'Seed Services Unit' => new SeedServicesUnitExport($this->template),

        ];
    }
}
