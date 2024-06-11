<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RtcProductionFarmerMainSheet implements FromCollection, WithTitle, WithHeadings
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    public function title(): string
    {
        return 'RTC_FARMERS';
    }
    public function collection()
    {
        $faker = Faker::create();
        $data = [];

        if ($this->test) {
            foreach (range(1, 20) as $index) {

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
                    'DATE OF RECRUITMENT' => $faker->date(),
                    'NAME OF ACTOR' => $faker->name(),
                    'NAME OF REPRESENTATIVE' => $faker->name(),
                    'PHONE NUMBER' => $faker->phoneNumber(),
                    'TYPE' => $faker->randomElement(['Type A', 'Type B', 'Type C']),
                    'APPROACH' => $faker->randomElement(['Approach 1', 'Approach 2', 'Approach 3']),
                    'SECTOR' => $faker->randomElement(['Sector 1', 'Sector 2', 'Sector 3']),
                    'NUMBER OF MEMBERS/TOTAL' => $faker->numberBetween(10, 100) * 10,
                    'NUMBER OF MEMBERS/FEMALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/FEMALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/MALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/MALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'GROUP' => $faker->randomElement(['Group 1', 'Group 2', 'Group 3']),
                    'ESTABLISHMENT STATUS' => $faker->randomElement(['NEW', 'OLD']),
                    'IS REGISTERED' => $faker->randomElement(['YES', "NO"]),
                    'REGISTRATION DETAILS/REGISTRATION BODY' => $faker->company(),
                    'REGISTRATION DETAILS/REGISTRATION NUMBER' => $faker->ean8(),
                    'REGISTRATION DETAILS/REGISTRATION DATE' => $faker->date(),
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CULTIVATION/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)' => $faker->randomElement(['Variety A', 'Variety B', 'Variety C']),
                    'AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)' => $faker->randomElement(['Variety X', 'Variety Y', 'Variety Z']),
                    'AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)' => $faker->randomElement(['Variety M', 'Variety N', 'Variety O']),
                    'AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)' => $faker->randomElement(['Variety P', 'Variety Q', 'Variety R']),
                    'AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)' => $faker->randomElement(['Variety S', 'Variety T', 'Variety U']),
                    'NUMBER OF PLANTLETS PRODUCED/CASSAVA' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF PLANTLETS PRODUCED/POTATO' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF PLANTLETS PRODUCED/SWEET POTATO' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF SCREEN HOUSE VINES HARVESTED' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF SAH PLANTS PRODUCED' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)' => $faker->numberBetween(1, 100) * 10,
                    'IS REGISTERED SEED PRODUCER' => $faker->randomElement(['YES', "NO"]),
                    'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER' => $faker->ean8(),
                    'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION DATE' => $faker->date(),
                    'USES CERTIFIED SEED' => $faker->randomElement(['YES', "NO"]),
                    'MARKET SEGMENT/FRESH' => $faker->randomElement(['YES', "NO"]),
                    'MARKET SEGMENT/PROCESSED' => $faker->randomElement(['YES', "NO"]),
                    'HAS RTC MARKET CONTRACT' => $faker->randomElement(['YES', "NO"]),
                    'TOTAL VOLUME PRODUCTION PREVIOUS SEASON' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES' => $faker->date(),
                    'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES' => $faker->date(),
                    'SELLS TO DOMESTIC MARKETS' => $faker->randomElement(['YES', "NO"]),
                    'SELLS TO INTERNATIONAL MARKETS' => $faker->randomElement(['YES', "NO"]),
                    'USES MARKET INFORMATION SYSTEMS' => $faker->randomElement(['YES', "NO"]),
                    'MARKET INFORMATION SYSTEMS' => $faker->randomElement(['System A', 'System B', 'System C']),
                    'SELLS TO AGGREGATION CENTERS' => $faker->randomElement(['YES', "NO"]),
                    'AGGREGATION CENTERS/RESPONSE' => $faker->randomElement(['YES', "NO"]),
                    'AGGREGATION CENTERS/SPECIFY' => $faker->streetName,
                    'TOTAL AGGREGATION CENTER SALES VOLUME' => $faker->numberBetween(1, 100) * 10,
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
            'DATE OF RECRUITMENT',
            'NAME OF ACTOR',
            'NAME OF REPRESENTATIVE',
            'PHONE NUMBER',
            'TYPE',
            'APPROACH',
            'SECTOR',
            'NUMBER OF MEMBERS/TOTAL',
            'NUMBER OF MEMBERS/FEMALE 18-35YRS',
            'NUMBER OF MEMBERS/FEMALE 35YRS+',
            'NUMBER OF MEMBERS/MALE 18-35YRS',
            'NUMBER OF MEMBERS/MALE 35YRS+',
            'GROUP',
            'ESTABLISHMENT STATUS',
            'IS REGISTERED',
            'REGISTRATION DETAILS/REGISTRATION BODY',
            'REGISTRATION DETAILS/REGISTRATION NUMBER',
            'REGISTRATION DETAILS/REGISTRATION DATE',
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)',
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS',
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+',
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS',
            'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+',
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)',
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS',
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+',
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS',
            'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+',
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
            'TOTAL VOLUME PRODUCTION PREVIOUS SEASON',
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL',
            'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES',
            'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON',
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
