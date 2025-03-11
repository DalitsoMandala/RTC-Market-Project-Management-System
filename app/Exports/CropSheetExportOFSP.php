<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\SeedBeneficiary;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CropSheetExportOFSP implements FromCollection, WithHeadings, WithTitle
{
    protected $cropType;
    public $template = false;
    public function __construct(string $cropType, $template = false)
    {
        $this->cropType = $cropType;
        $this->template = $template;
    }



    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        $data = SeedBeneficiary::where('crop', $this->cropType)->select(
            //  'crop',
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
            'national_id',
            'phone_number',
            'signed',
            'year'

        )->get();


        $data->transform(function ($row) {
            $row->date = Carbon::parse($row->date)->format('d-m-Y');
            return $row;
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            //     'Crop',
            'District',
            'EPA',
            'Section',
            'Name of AEDO',
            'AEDO Phone Number',
            'Date of Distribution',
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
            'National ID',
            'Phone Number',
            'Signed',
            'Year'
        ];
    }

    public function title(): string
    {
        return $this->cropType;
    }
}
