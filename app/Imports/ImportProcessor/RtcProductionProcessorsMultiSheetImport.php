<?php

namespace App\Imports\ImportProcessor;

use App\Imports\AbstractMultiSheetImport;
use App\Imports\ImportProcessor\RpmpAggregationCentersImport;
use App\Imports\ImportProcessor\RpmpMisImport;
use App\Imports\ImportProcessor\RpmProcessorConcAgreementsImport;
use App\Imports\ImportProcessor\RpmProcessorDomMarketsImport;
use App\Imports\ImportProcessor\RpmProcessorInterMarketsImport;
use App\Imports\ImportProcessor\RtcProductionProcessorsImport;
use App\Models\RtcProductionProcessor;

class RtcProductionProcessorsMultiSheetImport extends AbstractMultiSheetImport
{
    protected array $expectedSheetNames = [
        'Production Processors',
        'Contractual Agreements',
        'Domestic Markets',
        'International Markets',
        'Market Information Systems',
        'Aggregation Centers',
    ];

    protected function getTableName(): string
    {
        return 'rtc_production_processors';
    }

    protected function getFormName(): string
    {
        return 'Rtc Production Processors Form';
    }

    protected function getModelClass(): string
    {
        return RtcProductionProcessor::class;
    }

    protected function getSubmissionPeriodColumnName(): string
    {
        return 'reporting_period_month_id_period_month_id';
    }

    protected function getSheetRules(): array
    {
        return [
            'required' => [
                'Production Processors' => 2,
            ],
            'optional' => [
                'Contractual Agreements'     => 2,
                'Domestic Markets'           => 2,
                'International Markets'      => 2,
                'Market Information Systems' => 2,
                'Aggregation Centers'        => 2,
            ],
        ];
    }

    public function sheets(): array
    {
        return [
            'Production Processors'      => new RtcProductionProcessorsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Contractual Agreements'     => new RpmProcessorConcAgreementsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Domestic Markets'           => new RpmProcessorDomMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'International Markets'      => new RpmProcessorInterMarketsImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Market Information Systems' => new RpmpMisImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Aggregation Centers'        => new RpmpAggregationCentersImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }
}
