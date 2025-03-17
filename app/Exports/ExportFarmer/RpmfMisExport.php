<?php

namespace App\Exports\ExportFarmer;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\RpmFarmerMarketInformationSystem;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfMisExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    public $template;
    public $validationTypes = [

        'Name' => 'Text',
        'Farmer ID' => 'Exists in Production Farmers Sheet',
    ];

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns we want to include, excluding 'ID'
        return RpmFarmerMarketInformationSystem::select('name', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Exclude 'ID' from the headings
        return [
            [
                'Name',
                'Farmer ID'
            ],
            array_values($this->validationTypes)
        ];
    }

    public function title(): string
    {
        return 'Market Information Systems';
    }
}
