<?php

namespace App\Exports\RtcRecruitment;

use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SeedServicesUnitExport implements WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;

    use FormEssentials;


    protected $rowNumber = 0; // Start counting from 1
    public $template;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Recruitment Form']['Seed Services Unit'];
    }

    public function collection()
    {

        if ($this->template) {
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [

            array_keys($this->validationTypes),

            array_values($this->validationTypes)
        ];
    }

    public function title(): string
    {
        return 'Seed Services Unit';
    }
}
