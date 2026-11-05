<?php

namespace App\Imports\ImportFarmer;

use App\Imports\AbstractMultiSheetImport;
use App\Imports\ImportFarmer\RpmfAggregationCentersImport;
use App\Imports\ImportFarmer\RpmfAreaCultivationImport;
use App\Imports\ImportFarmer\RpmFarmerConcAgreementsImport;
use App\Imports\ImportFarmer\RpmFarmerDomMarketsImport;
use App\Imports\ImportFarmer\RpmFarmerInterMarketsImport;
use App\Imports\ImportFarmer\RpmfBasicSeedImport;
use App\Imports\ImportFarmer\RpmfCertifiedSeedImport;
use App\Imports\ImportFarmer\RpmfMisImport;
use App\Imports\ImportFarmer\RtcProductionFarmersImport;
use App\Models\RtcProductionFarmer;

class RtcProductionFarmersMultiSheetImport extends AbstractMultiSheetImport
{
    protected array $expectedSheetNames = [
        'Production Farmers',
        'Contractual Agreements',
        'Domestic Markets',
        'International Markets',
        'Market Information Systems',
        'Aggregation Centers',
        'Basic Seed',
        'Certified Seed',
        'Area Cultivation',
        'Seed Services Unit',
    ];

    protected function getTableName(): string
    {
        return 'rtc_production_farmers';
    }

    protected function getFormName(): string
    {
        return 'Rtc Production Farmers Form';
    }

    protected function getModelClass(): string
    {
        return RtcProductionFarmer::class;
    }

    protected function getSubmissionPeriodColumnName(): string
    {
        return 'reporting_period_month_id';
    }

    protected function getSheetRules(): array
    {
        return [
            'required' => [
                'Production Farmers' => 2,
            ],
            'optional' => [
                'Contractual Agreements'     => 2,
                'Domestic Markets'           => 2,
                'International Markets'      => 2,
                'Market Information Systems' => 2,
                'Aggregation Centers'        => 2,
                'Basic Seed'                 => 2,
                'Certified Seed'             => 2,
                'Area Cultivation'           => 2,
                'Seed Services Unit'         => 2,
            ],
        ];
    }

    public function sheets(): array
    {
        return [
            'Production Farmers'         => new RtcProductionFarmersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Contractual Agreements'     => new RpmFarmerConcAgreementsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Domestic Markets'           => new RpmFarmerDomMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'International Markets'      => new RpmFarmerInterMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Market Information Systems' => new RpmfMisImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Aggregation Centers'        => new RpmfAggregationCentersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Basic Seed'                 => new RpmfBasicSeedImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Certified Seed'             => new RpmfCertifiedSeedImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Area Cultivation'           => new RpmfAreaCultivationImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Seed Services Unit'         => new SeedServicesUnitImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }
}
