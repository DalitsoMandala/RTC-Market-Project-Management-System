<?php

namespace App\Exports\RtcConsumption;

use Carbon\Carbon;
use App\Models\RtcConsumption;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcConsumptionExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;
    public $template = false;
    public $validationTypes = [
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'District' => 'Required, Text',
        'Entity Name' => 'Required, Text',
        'Entity Type' => 'Required, Text',
        'Date' => 'Date (dd-mm-yyyy)',
        'Cassava Crop' => 'Boolean (1/0)',
        'Potato Crop' => 'Boolean (1/0)',
        'Sweet Potato Crop' => 'Boolean (1/0)',
        'Male Count' => 'Number (>=0)',
        'Female Count' => 'Number (>=0)',
    ];

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
        $data = RtcConsumption::select(

            'epa',
            'section',
            'district',
            'entity_name',
            'entity_type',
            'date',
            'crop_cassava',
            'crop_potato',
            'crop_sweet_potato',
            'male_count',
            'female_count',

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

            array_keys($this->validationTypes),
            array_values($this->validationTypes)

        ];
    }

    public function title(): string
    {
        return 'RTC Consumption';
    }
}
