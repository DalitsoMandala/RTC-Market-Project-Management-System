<?php

namespace App\Exports\ExportProcessor;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RtcProductionProcessorsMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new RtcProductionProcessorsExport(),
            new RpmProcessorConcAgreementsExport(),
            new RpmProcessorDomMarketsExport(),
            new RpmProcessorInterMarketsExport(),
            new RpmpMisExport(),
            new RpmpAggregationCentersExport(),
        ];
    }
}
