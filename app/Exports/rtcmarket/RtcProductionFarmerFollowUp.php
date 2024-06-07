<?php

namespace App\Exports\rtcmarket;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RtcProductionFarmerFollowUp implements FromCollection, WithTitle, WithHeadings
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    public function title(): string
    {
        return 'RTC PROD. FOLLOW UP';
    }
    public function collection()
    {
        $faker = Faker::create();
        $data = [];

        if ($this->test) {
            foreach (range(1, 5) as $index) {

                $data[] = [
                    'RECRUIT ID' => $faker->numberBetween(1, 20),
                    'ENTERPRISE' => strtoupper($faker->streetName),
                    'DISTRICT' => $faker->randomElement([
                        'BALAKA', 'BLANTYRE', 'CHIKWAWA', 'CHIRADZULU', 'CHITIPA', 'DEDZA', 'DOWA', 'KARONGA',
                        'KASUNGU', 'LILONGWE', 'MACHINGA', 'MANGOCHI', 'MCHINJI', 'MULANJE', 'MWANZA', 'MZIMBA',
                        'NENO', 'NKHATA BAY', 'NKHOTAKOTA', 'NSANJE', 'NTCHEU', 'NTCHISI', 'PHALOMBE', 'RUMPHI',
                        'SALIMA', 'THYOLO', 'ZOMBA',
                    ]),
                    'EPA' => strtoupper($faker->city),
                    'SECTION' => strtoupper($faker->streetName),
                    'DATE OF FOLLOW UP' => now(),
                    'AREA UNDER CULTIVATION/TOTAL' => $faker->randomFloat(2, 1, 1000),
                    'AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)' => $faker->randomElement(['Variety A', 'Variety B', 'Variety C']),
                    'AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)' => $faker->randomElement(['Variety X', 'Variety Y', 'Variety Z']),
                    'AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)' => $faker->randomElement(['Variety M', 'Variety N', 'Variety O']),
                    'AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)' => $faker->randomElement(['Variety P', 'Variety Q', 'Variety R']),
                    'AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)' => $faker->randomElement(['Variety S', 'Variety T', 'Variety U']),
                    'NUMBER OF PLANTLETS PRODUCED/CASSAVA' => $faker->numberBetween(1, 1000),
                    'NUMBER OF PLANTLETS PRODUCED/POTATO' => $faker->numberBetween(1, 1000),
                    'NUMBER OF PLANTLETS PRODUCED/SWEET POTATO' => $faker->numberBetween(1, 1000),
                    'NUMBER OF SCREEN HOUSE VINES HARVESTED' => $faker->numberBetween(1, 1000),
                    'NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED' => $faker->numberBetween(1, 1000),
                    'NUMBER OF SAH PLANTS PRODUCED' => $faker->numberBetween(1, 1000),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL' => $faker->randomFloat(2, 1, 1000),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL' => $faker->randomFloat(2, 1, 1000),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)' => $faker->randomFloat(2, 1, 100),
                    'IS REGISTERED SEED PRODUCER' => $faker->randomElement(['YES', null]),
                    'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER' => $faker->ean8(),
                    'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION DATE' => $faker->date(),
                    'USES CERTIFIED SEED' => $faker->randomElement(['YES', null]),
                    'MARKET SEGMENT/FRESH' => $faker->randomElement(['YES', "NO"]),
                    'MARKET SEGMENT/PROCESSED' => $faker->randomElement(['YES', "NO"]),
                    'HAS RTC MARKET CONTRACT' => $faker->randomElement(['YES', null]),
                    'TOTAL PRODUCTION PREVIOUS SEASON' => $faker->randomFloat(2, 1, 1000),
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL' => $faker->randomFloat(2, 1, 1000000),
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES' => $faker->date(),
                    'TOTAL IRRIGATION PRODUCTION PREVIOUS SEASON' => $faker->randomFloat(2, 1, 1000),
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL' => $faker->randomFloat(2, 1, 1000000),
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES' => $faker->date(),
                    'SELLS TO DOMESTIC MARKETS' => $faker->randomElement(['YES', null]),
                    'SELLS TO INTERNATIONAL MARKETS' => $faker->randomElement(['YES', null]),
                    'USES MARKET INFORMATION SYSTEMS' => $faker->randomElement(['YES', null]),
                    'MARKET INFORMATION SYSTEMS' => $faker->randomElement(['System A', 'System B', 'System C']),
                    'SELLS TO AGGREGATION CENTERS' => $faker->randomElement(['YES', null]),
                    'AGGREGATION CENTERS/RESPONSE' => $faker->randomElement(['YES', "NO"]),
                    'AGGREGATION CENTERS/SPECIFY' => $faker->streetName,
                    'TOTAL AGGREGATION CENTER SALES VOLUME' => $faker->randomFloat(2, 1, 1000),
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
            'ENTERPRISE',
            'DISTRICT',
            'EPA',
            'SECTION',
            'DATE OF FOLLOW UP',
            'AREA UNDER CULTIVATION/TOTAL',
            'AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)',
            'AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)',
            'AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)',
            'AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)',
            'AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)',
            'NUMBER OF PLANTLETS PRODUCED/CASSAVA',
            'NUMBER OF PLANTLETS PRODUCED/POTATO',
            'NUMBER OF PLANTLETS PRODUCED/SWEET POTATO',
            'NUMBER OF SCREEN HOUSE VINES HARVESTED',
            'NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED',
            'NUMBER OF SAH PLANTS PRODUCED',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)',
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)',
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)',
            'IS REGISTERED SEED PRODUCER',
            'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER',
            'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION DATE',
            'USES CERTIFIED SEED',
            'MARKET SEGMENT/FRESH',
            'MARKET SEGMENT/PROCESSED',
            'HAS RTC MARKET CONTRACT',
            'TOTAL PRODUCTION PREVIOUS SEASON',
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL',
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES',
            'TOTAL IRRIGATION PRODUCTION PREVIOUS SEASON',
            'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL',
            'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES',
            'SELLS TO DOMESTIC MARKETS',
            'SELLS TO INTERNATIONAL MARKETS',
            'USES MARKET INFORMATION SYSTEMS',
            'MARKET INFORMATION SYSTEMS',
            'SELLS TO AGGREGATION CENTERS',
            'AGGREGATION CENTERS/RESPONSE',
            'AGGREGATION CENTERS/SPECIFY',
            'TOTAL AGGREGATION CENTER SALES VOLUME',
        ];

    }
}