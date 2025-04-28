<?php

namespace App\Exports\ExportProcessor;


use App\Traits\ExportStylingTrait;
use App\Models\RpmpAggregationCenter;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\RpmProcessorAggregationCenter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmpAggregationCentersExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{

    use ExportStylingTrait;
    public $template;
    public $validationTypes = [

        'Name' => 'Text',
        'Processor ID' => 'Exists in Production Processors Sheet',
    ];
    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {

        if ($this->template) {
            return collect([]);  // Return an empty collection if the template is not provided.
        }
        return RpmProcessorAggregationCenter::select(
            'name',
            'rpmp_id'
        )->get();
    }

    public function headings(): array
    {
        return [
            [
                'Aggregation Center Name',
                'Processor ID'
            ],
            array_values($this->validationTypes)
        ];
    }

    public function title(): string
    {
        return 'Aggregation Centers';
    }
}
