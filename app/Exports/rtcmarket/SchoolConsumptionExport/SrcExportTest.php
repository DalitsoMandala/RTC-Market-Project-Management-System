<?php

namespace App\Exports\rtcmarket\SchoolConsumptionExport;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SrcExportTest implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $faker = Faker::create();
        $data = [];
        foreach (range(1, 15) as $index) {

            $data[] = [
                'ENTERPRISE' => strtoupper($faker->streetName),
                'DISTRICT' => $faker->randomElement([
                    'BALAKA', 'BLANTYRE', 'CHIKWAWA', 'CHIRADZULU', 'CHITIPA', 'DEDZA', 'DOWA', 'KARONGA',
                    'KASUNGU', 'LILONGWE', 'MACHINGA', 'MANGOCHI', 'MCHINJI', 'MULANJE', 'MWANZA', 'MZIMBA',
                    'NENO', 'NKHATA BAY', 'NKHOTAKOTA', 'NSANJE', 'NTCHEU', 'NTCHISI', 'PHALOMBE', 'RUMPHI',
                    'SALIMA', 'THYOLO', 'ZOMBA',
                ]),
                'EPA' => strtoupper($faker->city),
                'SECTION' => strtoupper($faker->streetName),
                'DATE' => $faker->date(),
                'CROP' => $faker->randomElement(['CASSAVA', 'POTATO', 'SWEET POTATO']),
                'MALES' => $faker->randomNumber(1, 900),
                'FEMALE' => $faker->randomNumber(1, 900),
                'TOTAL' => $faker->randomNumber(1, 900),

            ];
        }
        return collect([
            $data,
        ]);
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

}