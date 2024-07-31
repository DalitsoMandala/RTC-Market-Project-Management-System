<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use App\Helpers\ArrayToUpperCase;
use App\Helpers\Help;
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

        $epaNames = [
            'Malawi Environmental Protection Agency',
            'Lilongwe Environmental Authority',
            'Blantyre Environmental Conservation Agency',
            'Mzuzu Environmental Regulatory Authority',
            'Zomba Environmental Management Board',
        ];

        $sectionNames = [
            'Environmental Impact Assessment',
            'Pollution Control and Waste Management',
            'Natural Resources Management',
            'Environmental Education and Awareness',
            'Climate Change and Resilience',
        ];

        $organisationNames = [
            'Malawi Farmers Union',
            'Lilongwe Agricultural Cooperative',
            'Blantyre Horticultural Society',
            'Mzuzu Crop Producers Association',
            'Zomba Livestock Farmers Group',
        ];
        $epaNames = ArrayToUpperCase::convert($epaNames);
        $organisationNames = ArrayToUpperCase::convert($organisationNames);
        $sectionNames = ArrayToUpperCase::convert($sectionNames);

        if ($this->test) {
            $count = 1;
            foreach (range(1, 10) as $index) {

                $data[] = [
                    '#' => $count++,
                    'ENTERPRISE' => $faker->randomElement(Help::getFakerNames()['enterpriseNames']),
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
                    'EPA' => $faker->randomElement($epaNames),
                    'SECTION' => $faker->randomElement($sectionNames),
                    'DATE OF RECRUITMENT' => $faker->date(),
                    'NAME OF ACTOR' => strtoupper($faker->name()),
                    'NAME OF REPRESENTATIVE' => strtoupper($faker->name()),
                    'PHONE NUMBER' => $faker->numerify('###-###-####'),
                    'TYPE' => $faker->randomElement([
                        "PRODUCER ORGANIZATION (PO)",
                        "LARGE SCALE FARM",
                    ]),
                    'APPROACH' => $faker->randomElement([
                        'COLLECTIVE PRODUCTION ONLY',
                        'COLLECTIVE MARKETING ONLY',
                        'KNOWLEDGE SHARING ONLY',
                        'COLLECTIVE PRODUCTION, MARKETING AND KNOWLEDGE SHARING',
                        'N/A',
                    ]),
                    'SECTOR' => $faker->randomElement(['PRIVATE', 'PUBLIC']),
                    'NUMBER OF MEMBERS/TOTAL' => $faker->numberBetween(10, 100) * 10,
                    'NUMBER OF MEMBERS/FEMALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/FEMALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/MALE 18-35YRS' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/MALE 35YRS+' => $faker->numberBetween(1, 100) * 10,
                    'GROUP' => $faker->randomElement(['EARLY GENERATION SEED PRODUCER', 'SEED MULTIPLIER', 'RTC PRODUCER']),
                    'ESTABLISHMENT STATUS' => $faker->randomElement(['NEW', 'OLD']),
                    'IS REGISTERED' => $faker->randomElement(['YES', "NO"]),
                    'REGISTRATION DETAILS/REGISTRATION BODY' => strtoupper($faker->company()),
                    'REGISTRATION DETAILS/REGISTRATION NUMBER' => $faker->regexify('[A-Z]{5}[0-4]{3}'),
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
                    'AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'NUMBER OF PLANTLETS PRODUCED/CASSAVA' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF PLANTLETS PRODUCED/POTATO' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF PLANTLETS PRODUCED/SWEET POTATO' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF SCREEN HOUSE VINES HARVESTED' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF SAH PLANTS PRODUCED' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)' => strtoupper($faker->randomElement(['Variety A', 'Variety B', 'Variety C'])),
                    'IS REGISTERED SEED PRODUCER' => $faker->randomElement(['YES', "NO"]),
                    'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER' => $faker->regexify('[A-Z]{5}[0-4]{3}'),
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
                    'MARKET INFORMATION SYSTEMS' => strtoupper($faker->randomElement(['System A', 'System B', 'System C'])),
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
            '#',
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
