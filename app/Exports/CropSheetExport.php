<?php

namespace App\Exports;

use App\Models\SeedBeneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CropSheetExport implements FromCollection, WithHeadings, WithTitle
{
    protected $cropType;

    public function __construct(string $cropType)
    {
        $this->cropType = $cropType;
    }

    public function collection()
    {
        return SeedBeneficiary::where('crop', $this->cropType)->select(
            'crop',
            'district',
            'epa',
            'section',
            'name_of_aedo',
            'aedo_phone_number',
            'date',
            'name_of_recipient',
            'village',
            'sex',
            'age',
            'marital_status',
            'hh_head',
            'household_size',
            'children_under_5',
            'variety_received',
            'bundles_received',
            'phone_or_national_id'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Crop',
            'District',
            'EPA',
            'Section',
            'Name of AEDO',
            'AEDO Phone Number',
            'Date',
            'Name of Recipient',
            'Village',
            'Sex',
            'Age',
            'Marital Status',
            'Household Head',
            'Household Size',
            'Children Under 5 in HH',
            'Variety Received',
            'Bundles Received',
            'Phone / National ID',
        ];
    }

    public function title(): string
    {
        return $this->cropType;
    }
}
