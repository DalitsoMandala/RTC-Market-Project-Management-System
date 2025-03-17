<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmFarmerAreaCultivation;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfAreaCultivationExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    public $template;
    public $validationTypes = [



        'Variety' => 'Text',
        'Area' => 'Number (>=0)',
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
        // Select only the columns we want to include, excluding 'ID', 'Created At', and 'Updated At'
        return RpmFarmerAreaCultivation::select('variety', 'area', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return [
            [
                'Variety',
                'Area',
                'Farmer ID'
            ],
            array_values($this->validationTypes)
        ];
    }


    public function title(): string
    {
        return 'Area Cultivation';
    }
}
