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

class CropSheetExportCassava extends CropSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{

    public $validationTypes = [
        'District' => 'Required, Text',
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'Name of AEDO' => 'Required, Text',
        'AEDO Phone Number' => 'Text',
        'Date of Distribution' => 'Date (dd-mm-yyyy)',
        'Name of Recipient' => 'Required, Text',
        'Group Name' => 'Text',
        'Village' => 'Text',
        'Sex' => 'Required, Male/Female',
        'Age' => 'Required, Number (>=1)',
        'Marital Status' => 'Number',
        'Household Head' => 'Number (>=1)',
        'Household Size' => 'Number (>=1)',
        'Children Under 5 in HH' => 'Number (>=0)',
        'Variety Received' => 'Text, (IF Multiple separate by commas)',
        'Amount Received' => 'Number(>=0)',
        'National ID' => 'Text',
        'Phone Number' => 'Text',
        'Signed' => 'Number (>=0)',
        'Year' => 'Number',
        'Season Type' => 'Text, (Choose One)'
    ];


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
                'Group Name',
                'Village',
                'Sex',
                'Age',
                'Marital Status',
                'Household Head',
                'Household Size',
                'Children Under 5 in HH',
                'Variety Received',
                'Amount Received',
                'National ID',
                'Phone Number',
                'Signed',
                'Year',
                'Season Type'
            ],
            array_values($this->validationTypes)
        ];
    }
}
