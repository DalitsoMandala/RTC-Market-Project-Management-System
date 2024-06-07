<?php

namespace App\Exports\rtcmarket;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RtcProductionFarmerConcAgreement implements FromCollection, WithTitle, WithHeadings
{public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    public function title(): string
    {
        return 'RTC PROD. CONC_AGR';
    }
    public function collection()
    {
        $faker = Faker::create();
        $data = [];

        if($this->test){
        foreach (range(1, 5) as $index) {

            $data[] = [
                'RECRUIT ID' => $faker->numberBetween(1, 20),
                'DATE RECORDED' => $faker->date('d-m-Y', 'now'),
                'PARTNER NAME' => $faker->name,
                'COUNTRY' => $faker->country,
                'DATE OF MAXIMUM SALE' => $faker->date,
                'PRODUCT TYPE' => $faker->randomElement(['SEED', 'WARE', 'VALUE ADDED PRODUCTS']),
                'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)' => $faker->randomFloat(2, 1, 20.50),
                'FINANCIAL VALUE OF SALES (MALAWI KWACHA)' => $faker->randomFloat(2, 1, 223420.50),

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

    }}
