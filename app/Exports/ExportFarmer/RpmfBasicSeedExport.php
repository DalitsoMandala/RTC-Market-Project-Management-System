<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmfBasicSeed;
use App\Models\RpmFarmerBasicSeed;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfBasicSeedExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
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
        return RpmFarmerBasicSeed::select('variety', 'area', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        return [
            'Variety',
            'Area',
            'Farmer ID'
        ];
    }


    public function title(): string
    {
        return 'Basic Seed';
    }
}
