<?php

namespace App\Exports\rtcmarket\HouseholdExport;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class HrcExport implements FromCollection, WithHeadings, WithTitle
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    public function collection()
    {
        $faker = Faker::create();
        $data = [];

        if ($this->test) {
            foreach (range(1, 5) as $index) {

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
                    'DATE OF ASSESSMENT' => $faker->date(),
                    'ACTOR TYPE' => $faker->randomElement(['FARMER', 'PROCESSOR', 'TRADER', 'INDIVIDUALS FROM NUTRITION INTERVENTION', 'OTHER']),
                    'RTC GROUP PLATFORM' => $faker->randomElement(['HOUSEHOLD', 'SEED']),
                    'PRODUCER ORGANISATION' => strtoupper($faker->company),
                    'ACTOR NAME' => strtoupper($faker->name),
                    'AGE GROUP' => $faker->randomElement(['YOUTH', 'NOT YOUTH']),
                    'SEX' => $faker->randomElement(['MALE', 'FEMALE']),
                    'PHONE NUMBER' => $faker->phoneNumber,
                    'HOUSEHOLD SIZE' => $faker->numberBetween(1, 10),
                    'UNDER 5 IN HOUSEHOLD' => $faker->numberBetween(0, 5),
                    'RTC CONSUMERS' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS/POTATO' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS/SWEET POTATO' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS/CASSAVA' => $faker->numberBetween(1, 100),
                    'RTC CONSUMPTION FREQUENCY' => $faker->numberBetween(1, 10),
                    'RTC MAIN FOOD/CASSAVA' => $faker->randomElement(['YES', null]),
                    'RTC MAIN FOOD/POTATO' => $faker->randomElement(['YES', null]),
                    'RTC MAIN FOOD/SWEET POTATO' => $faker->randomElement(['YES', null]),

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
            'ENTERPRISE',
            'DISTRICT',
            'EPA',
            'SECTION',
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
        return 'HH_CONSUMPTION';
    }
}