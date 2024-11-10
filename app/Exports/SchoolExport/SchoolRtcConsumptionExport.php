<?php

namespace App\Exports\SchoolExport;

use App\Models\SchoolRtcConsumption;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SchoolRtcConsumptionExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns to be included in the export
        return SchoolRtcConsumption::select(
            'sc_id',
            'epa',
            'section',
            'district',
            'school_name',
            'date',
            //   'crop',
            'crop_cassava',
            'crop_potato',
            'crop_sweet_potato',
            'male_count',
            'female_count',
            //  'total'
        )->get();
    }

    public function headings(): array
    {
        return [
            'School ID',
            'EPA',
            'Section',
            'District',
            'School Name',
            'Date',
            // 'Crop',
            'Cassava Crop',
            'Potato Crop',
            'Sweet Potato Crop',
            'Male Count',
            'Female Count',
            //   'Total',
        ];
    }

    public function title(): string
    {
        return 'School RTC Consumption';
    }
}
