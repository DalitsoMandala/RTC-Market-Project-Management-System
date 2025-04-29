<?php

namespace App\Exports\RtcConsumption;

use Carbon\Carbon;
use App\Models\RtcConsumption;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcConsumptionExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;
    use FormEssentials;
    public $template = false;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Consumption Form']['Rtc Consumption'];
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
        return 'RTC Consumption';
    }
}
