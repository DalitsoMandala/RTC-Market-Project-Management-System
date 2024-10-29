<?php

namespace App\Exports\ExportFarmer;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionFarmersMultiSheetExport implements WithMultipleSheets, WithStrictNullComparison
{
    public function sheets(): array
    {
        return [
            'Production Farmers' => new RtcProductionFarmersExport(),
            'Conc Agreements' => new RpmFarmerConcAgreementsExport(),
            'Domestic Markets' => new RpmFarmerDomMarketsExport(),
            'International Markets' => new RpmFarmerInterMarketsExport(),
            'Market Info Systems' => new RpmfMisExport(),
            'Aggregation Centers' => new RpmfAggregationCentersExport(),
            'Basic Seed' => new RpmfBasicSeedExport(),
            'Certified Seed' => new RpmfCertifiedSeedExport(),
            'Area Cultivation' => new RpmfAreaCultivationExport(),
        ];
    }
}
