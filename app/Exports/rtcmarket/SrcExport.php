<?php

namespace App\Exports\rtcmarket;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SrcExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'ENTERPRISE',
            'DISTRICT',
            'EPA',
            'SECTION',
            'DATE',
            'CROP',
            'MALES',
            'FEMALE',
            'TOTAL',

        ];

    }

    public function title(): string
    {
        return 'Sheet 1';
    }
}
