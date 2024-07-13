<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RtcProductionProcessorConcAgreement implements FromCollection, WithTitle, WithHeadings
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */

    public function title(): string
    {
        return 'RTC_PROC_AGREEMENT';
    }

    public function collection()
    {
        $faker = Faker::create();
        $data = [];

        if ($this->test) {
            foreach (range(1, 5) as $index) {

                $data[] = [
                    'RECRUIT ID' => $faker->numberBetween(1, 20),
                    'DATE RECORDED' => $faker->date(),
                    'PARTNER NAME' => strtoupper($faker->name),
                    'COUNTRY' => strtoupper($faker->country),
                    'DATE OF MAXIMUM SALE' => $faker->date,
                    'PRODUCT TYPE' => $faker->randomElement(['SEED', 'WARE', 'VALUE ADDED PRODUCTS']),
                    'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)' => $faker->numberBetween(1, 100) * 10,
                    'FINANCIAL VALUE OF SALES (MALAWI KWACHA)' => $faker->numberBetween(1, 100) * 10,

                ];
            }
        }
        return collect([
            $data,
        ]);

    }

    public function headings(): array
    {
        return [
            'RECRUIT ID',
            'DATE RECORDED',
            'PARTNER NAME',
            'COUNTRY',
            'DATE OF MAXIMUM SALE',
            'PRODUCT TYPE',
            'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)',
            'FINANCIAL VALUE OF SALES (MALAWI KWACHA)',
        ];

    }
}