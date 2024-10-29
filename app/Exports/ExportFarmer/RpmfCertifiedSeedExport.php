<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmFarmerCertifiedSeed;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class RpmfCertifiedSeedExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Select only the columns 'variety', 'area', and 'rpmf_id' (Farmer ID)
        return RpmFarmerCertifiedSeed::select('variety', 'area', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return ['Variety', 'Area', 'Farmer ID'];
    }


    public function title(): string
    {
        return 'Certified Seed';
    }
}
