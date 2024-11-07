<?php

namespace App\Exports\rtcmarket\SchoolConsumptionExport;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SrcExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct($test = false)
    {

        $this->test = $test;
    }

    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        $faker = Faker::create();
        $data = [];
        foreach (range(1, 15) as $index) {

            $data[] = [
                'SCHOOL NAME' => strtoupper($faker->streetName),
                'DISTRICT' => $faker->randomElement([
                    'BALAKA',
                    'BLANTYRE',
                    'CHIKWAWA',
                    'CHIRADZULU',
                    'CHITIPA',
                    'DEDZA',
                    'DOWA',
                    'KARONGA',
                    'KASUNGU',
                    'LILONGWE',
                    'MACHINGA',
                    'MANGOCHI',
                    'MCHINJI',
                    'MULANJE',
                    'MWANZA',
                    'MZIMBA',
                    'NENO',
                    'NKHATA BAY',
                    'NKHOTAKOTA',
                    'NSANJE',
                    'NTCHEU',
                    'NTCHISI',
                    'PHALOMBE',
                    'RUMPHI',
                    'SALIMA',
                    'THYOLO',
                    'ZOMBA',
                ]),
                'EPA' => strtoupper($faker->city),
                'SECTION' => strtoupper($faker->streetName),
                'DATE' => $faker->date(),
                'CROP' => $faker->randomElement([
                    'CASSAVA',
                    'POTATO',
                    'SWEET POTATO'
                ]),
                'MALES' => $faker->randomNumber(1, 100) * 10,
                'FEMALE' => $faker->randomNumber(1, 100) * 10,
                'TOTAL' => $faker->randomNumber(1, 100) * 10,

            ];
        }
        return collect([
            $data,
        ]);
    }

    public function headings(): array
    {
        return [
            'SCHOOL NAME',
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
        return 'SCHOOL_CONSUMPTION';
    }
}
