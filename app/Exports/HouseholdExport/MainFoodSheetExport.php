<?php

namespace App\Exports\HouseholdExport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class MainFoodSheetExport implements FromArray, WithHeadings, WithTitle, WithStrictNullComparison
{
    public function headings(): array
    {
        return [
            'Household ID',
            'Main Food Name',
        ];
    }

    public function array(): array
    {
        return [
            // Placeholder Household IDs, each associated with main foods
            [1, 'Maize'],
            [1, 'Rice'],
            [1, 'Beans'],
            [2, 'Cassava'],
            [2, 'Sweet Potato'],
            [2, 'Yam'],
            // Add more rows as needed for the template
        ];
    }

    public function title(): string
    {
        return 'Main Food Data';
    }
}
