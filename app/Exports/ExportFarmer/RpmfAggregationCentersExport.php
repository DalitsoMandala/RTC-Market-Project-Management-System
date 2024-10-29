<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmFarmerAggregationCenter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfAggregationCentersExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{

    public function collection()
    {
        // Select only the columns we want to include, excluding 'ID', 'Created At', and 'Updated At'
        return RpmFarmerAggregationCenter::select('name', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return ['Name', 'Farmer ID'];
    }

    public function title(): string
    {
        return 'Aggregation Centers';
    }
}
