<?php

namespace App\Exports\HouseholdExport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class HouseholdSheetExport implements FromArray, WithHeadings, WithTitle, WithStrictNullComparison
{
    protected $uuid;
    protected $userId;
    protected $submissionPeriodId;
    protected $organisationId;
    protected $financialYearId;
    protected $reportingPeriodMonthId;
    protected $status;

    public function __construct($uuid = null, $userId = null, $submissionPeriodId = null, $organisationId = null, $financialYearId = null, $reportingPeriodMonthId = null, $status = null)
    {
        $this->uuid = $uuid;
        $this->userId = $userId;
        $this->submissionPeriodId = $submissionPeriodId;
        $this->organisationId = $organisationId;
        $this->financialYearId = $financialYearId;
        $this->reportingPeriodMonthId = $reportingPeriodMonthId;
        $this->status = $status;
    }

    public function headings(): array
    {
        return [
            'ID',
            'EPA',
            'Section',
            'District',
            'Enterprise Type',
            'Date of Assessment',
            'Actor Type (Farmer, Trader, etc.)',
            'RTC Group/Platform',
            'Producer Organisation',
            'Actor Name',
            'Age Group',
            'Sex',
            'Phone Number',
            'Household Size',
            'Under 5 in Household',
            'RTC Consumers (Total)',
            'RTC Consumers - Potato',
            'RTC Consumers - Sweet Potato',
            'RTC Consumers - Cassava',
            'RTC Consumption Frequency',
            // Exclude hidden fields and Household ID
        ];
    }

    public function array(): array
    {
        return [
            [

                1,
                'EPA1',
                'Section1',
                'Zomba',
                'Cassava',
                '2024-12-10',
                'Farmer',
                'RTC Group 1',
                'Org1',
                'Actor Name',
                '18-25',
                'Male',
                '+2651234567890',
                5,
                2,
                3,
                1,
                1,
                1,
                5,
            ],
            [
                1,
                'EPA2',
                'Section2',
                'Lilongwe',
                'Potato',
                '2024-12-10',
                'Trader',
                'RTC Group 2',
                'Org2',
                'Actor Name 2',
                '26-35',
                'Female',
                '+265987654321',
                4,
                1,
                2,
                1,
                1,
                1,
                3,
            ],

        ];
    }

    public function title(): string
    {
        return 'Household Data';
    }
}
