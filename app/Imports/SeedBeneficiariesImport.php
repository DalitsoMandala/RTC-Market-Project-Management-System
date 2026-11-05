<?php

namespace App\Imports;

use App\Imports\AbstractMultiSheetImport;
use App\Imports\CropSheetImport;
use App\Models\SeedBeneficiary;

class SeedBeneficiariesImport extends AbstractMultiSheetImport
{
    protected array $expectedSheetNames = [
        'Potato',
        'Sweet potato',
        'Cassava',
    ];

    protected function getTableName(): string
    {
        return 'seed_beneficiaries';
    }

    protected function getFormName(): string
    {
        return 'Seed Beneficiaries Form';
    }

    protected function getModelClass(): string
    {
        return SeedBeneficiary::class;
    }

    protected function getSubmissionPeriodColumnName(): string
    {
        return 'submission_period_new_id';
    }

    protected function getSheetRules(): array
    {
        return [
            'required' => [],
            'optional' => [
                'Potato'       => 2,
                'Sweet potato' => 2,
                'Cassava'      => 2,
            ],
        ];
    }

    public function sheets(): array
    {
        return [
            'Potato'       => new CropSheetImport('Potato', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Sweet potato' => new CropSheetImportOFSP('Sweet potato', $this->submissionDetails, $this->cacheKey, $this->totalRows),
            'Cassava'      => new CropSheetImportCassava('Cassava', $this->submissionDetails, $this->cacheKey, $this->totalRows),
        ];
    }
}
