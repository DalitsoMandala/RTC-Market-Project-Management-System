<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmFarmerCertifiedSeed;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfCertifiedSeedExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns 'variety', 'area', and 'rpmf_id' (Farmer ID)
        return RpmFarmerCertifiedSeed::select('variety', 'area', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return [
            'Variety',
            'Area',
            'Farmer ID'
        ];
    }


    public function title(): string
    {
        return 'Certified Seed';
    }
}
