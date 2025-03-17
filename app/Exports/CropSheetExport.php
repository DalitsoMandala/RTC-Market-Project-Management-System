<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\SeedBeneficiary;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class CropSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    protected $cropType;
    public $template = false;
    public $validationTypes = [
        'District' => 'Required, Text',
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'Name of AEDO' => 'Required, Text',
        'AEDO Phone Number' => 'Text',
        'Date of Distribution' => 'Date (dd-mm-yyyy)',
        'Name of Recipient' => 'Required, Text',
        'Village' => 'Text',
        'Sex' => 'Required, Male/Female',
        'Age' => 'Required, Number (>=1)',
        'Marital Status' => 'Number',
        'Household Head' => 'Number (>=1)',
        'Household Size' => 'Number (>=1)',
        'Children Under 5 in HH' => 'Number (>=0)',
        'Variety Received' => 'Text, (IF Multiple separate by commas)',
        'Bundles Received' => 'Number(>=0) (KG)',
        'National ID' => 'Text',
        'Phone Number' => 'Text',
        'Signed' => 'Number (>=0)',
        'Year' => 'Number',
    ];
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

            [
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
                'Amount Of Seed Received',
                'National ID',
                'Phone Number',
                'Signed',
                'Year'
            ],
            array_values($this->validationTypes)
        ];
    }

    public function title(): string
    {
        return $this->cropType;
    }
}