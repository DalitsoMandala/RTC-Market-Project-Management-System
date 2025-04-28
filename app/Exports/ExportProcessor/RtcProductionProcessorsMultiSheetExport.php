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
            'Production Processors' =>  new RtcProductionProcessorsExport($this->template),
            'Contractual Agreements' =>  new RpmProcessorConcAgreementsExport($this->template),
            'Domestic Markets'   =>    new RpmProcessorDomMarketsExport($this->template),
            'International Markets' =>    new RpmProcessorInterMarketsExport($this->template),
            'Market Information Systems' => new RpmpMisExport($this->template),
            'Aggregation Centers' =>     new RpmpAggregationCentersExport($this->template),
        ];
    }
}
