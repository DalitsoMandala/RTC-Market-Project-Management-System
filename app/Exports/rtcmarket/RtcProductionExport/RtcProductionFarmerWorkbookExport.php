<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerConcAgreement;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerDomMarkets;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerFollowUp;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerInterMarkets;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerMainSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RtcProductionFarmerWorkbookExport implements FromCollection, WithMultipleSheets
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
    public function collection()
    {
        return collect([]);
    }

    public function sheets(): array
    {
        $sheets = [
            new RtcProductionFarmerMainSheet($this->test),
            new RtcProductionFarmerFollowUp($this->test),
            new RtcProductionFarmerConcAgreement($this->test),
            new RtcProductionFarmerDomMarkets($this->test),
            new RtcProductionFarmerInterMarkets($this->test),
        ];

        return $sheets;
    }
}