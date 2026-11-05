<?php
namespace App\Imports\RtcConsumption;

use App\Imports\AbstractMultiSheetImport;
use App\Imports\RtcConsumption\RtcConsumptionImport;
use App\Models\RtcConsumption;

class RtcConsumptionMultiSheet extends AbstractMultiSheetImport
{
    protected array $expectedSheetNames = [
        'Rtc Consumption',
    ];

    protected function getTableName(): string
    {
        return 'rtc_consumptions';
    }

    protected function getFormName(): string
    {
        return 'Rtc Consumption Form';
    }

    protected function getModelClass(): string
    {
        return RtcConsumption::class;
    }

    protected function getSubmissionPeriodColumnName(): string
    {
        return 'submission_period_new_id';
    }

    protected function getSheetRules(): array
    {
        return [
            'required' => [
                'Rtc Consumption' => 2,
            ],
            'optional' => [],
        ];
    }

    public function sheets(): array
    {
        return [
            'Rtc Consumption' => new RtcConsumptionImport($this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }
}
