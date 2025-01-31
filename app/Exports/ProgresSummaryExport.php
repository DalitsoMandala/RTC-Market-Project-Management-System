<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProgresSummaryExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Date',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Progress Summary';
    }
}
