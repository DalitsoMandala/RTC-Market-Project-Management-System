<?php
namespace App\Imports\MarketingLog;

use App\Imports\AbstractMultiSheetImport;
use App\Imports\MarketingLog\MarketingLogImport;
use App\Models\ProductionMarketingLog;

class MarketingLogImportMultiSheet extends AbstractMultiSheetImport
{
    protected array $expectedSheetNames = [
        'RTC-Market Data',
    ];

    protected function getTableName(): string
    {
        return 'production_marketing_logs';
    }

    protected function getFormName(): string
    {
        return 'Production and Marketing Log';
    }

    protected function getModelClass(): string
    {
        return ProductionMarketingLog::class;
    }

    protected function getSubmissionPeriodColumnName(): string
    {
        return 'submission_period_new_id';
    }

    protected function getSheetRules(): array
    {
        return [
            'required' => [
                'RTC-Market Data' => 2,
            ],
            'optional' => [],
        ];
    }

    public function sheets(): array
    {
        return [
            'RTC-Market Data' => new MarketingLogImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }
}
