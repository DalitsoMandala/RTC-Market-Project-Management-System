<?php

namespace App\Exports\rtcmarket;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class HrcExport implements FromCollection, WithHeadings, WithTitle
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
            'DATE OF ASSESSMENT',
            'ACTOR TYPE',
            'RTC GROUP PLATFORM',
            'PRODUCER ORGANISATION',
            'ACTOR NAME',
            'AGE GROUP',
            'SEX',
            'PHONE NUMBER',
            'HOUSEHOLD SIZE',
            'UNDER 5 IN HOUSEHOLD',
            'RTC CONSUMERS',
            'RTC CONSUMERS/POTATO',
            'RTC CONSUMERS/SWEET POTATO',
            'RTC CONSUMERS/CASSAVA',
            'RTC CONSUMPTION FREQUENCY',
            'RTC MAIN FOOD/CASSAVA',
            'RTC MAIN FOOD/POTATO',
            'RTC MAIN FOOD/SWEET POTATO',

        ];

    }

    public function title(): string
    {
        return 'HRC_Sheet';
    }
}
