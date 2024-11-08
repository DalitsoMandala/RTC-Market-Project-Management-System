<?php

namespace App\Exports\ExportProcessor;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RtcProductionProcessorsMultiSheetExport implements WithMultipleSheets
{
    use Exportable;
    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            new RtcProductionProcessorsExport($this->template),
            new RpmProcessorConcAgreementsExport($this->template),
            new RpmProcessorDomMarketsExport($this->template),
            new RpmProcessorInterMarketsExport($this->template),
            new RpmpMisExport($this->template),
            new RpmpAggregationCentersExport($this->template),
        ];
    }
}
