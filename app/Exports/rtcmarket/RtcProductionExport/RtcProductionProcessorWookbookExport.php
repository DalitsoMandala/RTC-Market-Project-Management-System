<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorConcAgreement;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorDomMarkets;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorFollowUp;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorInterMarkets;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorMainSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RtcProductionProcessorWookbookExport implements FromCollection, WithMultipleSheets
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        return collect([]);
    }

    public function sheets(): array
    {
        $sheets = [
            new RtcProductionProcessorMainSheet($this->test),
            new RtcProductionProcessorFollowUp($this->test),
            new RtcProductionProcessorConcAgreement($this->test),
            new RtcProductionProcessorDomMarkets($this->test),
            new RtcProductionProcessorInterMarkets($this->test),
        ];

        return $sheets;
    }
}
