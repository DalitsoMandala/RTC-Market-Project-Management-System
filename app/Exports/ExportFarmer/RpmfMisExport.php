<?php

namespace App\Exports\ExportFarmer;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\RpmFarmerMarketInformationSystem;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfMisExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public function collection()
    {
        // Select only the columns we want to include, excluding 'ID'
        return RpmFarmerMarketInformationSystem::select('name', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Exclude 'ID' from the headings
        return ['Name', 'Farmer ID'];
    }

    public function title(): string
    {
        return 'Market Information Systems';
    }
}
