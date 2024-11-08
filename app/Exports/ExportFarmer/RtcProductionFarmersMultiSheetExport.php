<?php

namespace App\Exports\ExportFarmer;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionFarmersMultiSheetExport implements WithMultipleSheets, WithStrictNullComparison
{

    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'Production Farmers' => new RtcProductionFarmersExport($this->template),
            'Conc Agreements' => new RpmFarmerConcAgreementsExport($this->template),
            'Domestic Markets' => new RpmFarmerDomMarketsExport($this->template),
            'International Markets' => new RpmFarmerInterMarketsExport($this->template),
            'Market Info Systems' => new RpmfMisExport($this->template),
            'Aggregation Centers' => new RpmfAggregationCentersExport($this->template),
            'Basic Seed' => new RpmfBasicSeedExport($this->template),
            'Certified Seed' => new RpmfCertifiedSeedExport($this->template),
            'Area Cultivation' => new RpmfAreaCultivationExport($this->template),
        ];
    }
}
