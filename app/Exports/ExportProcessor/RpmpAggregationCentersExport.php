<?php

namespace App\Exports\ExportProcessor;


use App\Models\RpmpAggregationCenter;
use App\Models\RpmProcessorAggregationCenter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmpAggregationCentersExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public function collection()
    {
        return RpmProcessorAggregationCenter::select(
            'name',
            'rpmp_id'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Aggregation Center Name',
            'Processor ID'
        ];
    }

    public function title(): string
    {
        return 'Aggregation Centers';
    }
}
