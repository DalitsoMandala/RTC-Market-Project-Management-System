<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use App\Helpers\Help;
use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RtcProductionProcessorMainSheet implements FromCollection, WithTitle, WithHeadings
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    public function title(): string
    {
        return 'RTC_PROCESSORS';
    }
    public function collection()
    {

        $faker = Faker::create();
        $data = [];
        $count = 1;
        if ($this->test) {
            foreach (range(1, 10) as $index) {
                $data[] = [
                    '#' => $count++,
                    'ENTERPRISE' => $faker->randomElement(['CASSAVA', 'POTATO', 'SWEET POTATO']),
                    'DISTRICT' => $faker->randomElement(Help::getFakerNames()['districtNames']),
                    'EPA' => $faker->randomElement(Help::getFakerNames()['epaNames']),
                    'SECTION' => $faker->randomElement(Help::getFakerNames()['sectionNames']),
                    'DATE OF RECRUITMENT' => $faker->date('Y-m-d'),
                    'NAME OF ACTOR' => $faker->randomElement(Help::getFakerNames()['organisationNames']),
                    'NAME OF REPRESENTATIVE' => strtoupper($faker->name),
                    $faker->numerify('###-###-####'),
                    'TYPE' => strtoupper($faker->randomElement([
                        'Producer organization (PO)',
                        'Large scale Processor',
                        'Small Medium Enterprise (SME)',
                    ])),
                    'APPROACH' => strtoupper($faker->optional()->word), // FOR PRODUCER ORGANIZATIONS ONLY
                    'SECTOR' => strtoupper($faker->word),
                    'NUMBER OF MEMBERS/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'NUMBER OF MEMBERS/FEMALE 18-35YRS' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF MEMBERS/FEMALE 35YRS+' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF MEMBERS/MALE 18-35YRS' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF MEMBERS/MALE 35YRS+' => $faker->numberBetween(1, 10) * 10, // FOR PRODUCER ORGANIZATIONS ONLY
                    'GROUP' => strtoupper($faker->word),
                    'ESTABLISHMENT STATUS' => $faker->randomElement(['NEW', 'OLD']), // UPPERCASE FOR ENUM VALUES
                    'IS REGISTERED' => $faker->randomElement(['Yes', 'No']),
                    'REGISTRATION DETAILS/REGISTRATION BODY' => strtoupper($faker->company),
                    'REGISTRATION DETAILS/REGISTRATION NUMBER' => $faker->uuid,
                    'REGISTRATION DETAILS/REGISTRATION DATE' => $faker->date('Y-m-d'),
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)' => $faker->randomNumber,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)' => $faker->randomNumber,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS' => $faker->numberBetween(1, 10) * 10,
                    'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+' => $faker->numberBetween(1, 10) * 10,
                    'MARKET SEGMENT/Fresh' => $faker->randomElement(['Yes', 'No']),
                    'MARKET SEGMENT/Processed' => $faker->randomElement(['Yes', 'No']), // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
                    'HAS RTC MARKET CONTRACT' => $faker->randomElement(['Yes', 'No']),
                    'TOTAL VOLUME PRODUCTION PREVIOUS SEASON' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES' => $faker->date('Y-m-d'),
                    'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES' => $faker->date('Y-m-d'),
                    'SELLS TO DOMESTIC MARKETS' => $faker->randomElement(['Yes', 'No']),
                    'SELLS TO INTERNATIONAL MARKETS' => $faker->randomElement(['Yes', 'No']),
                    'USES MARKET INFORMATION SYSTEMS' => $faker->randomElement(['Yes', 'No']),
                    'MARKET INFORMATION SYSTEMS' => strtoupper($faker->text),
                    'SELLS TO AGGREGATION CENTERS' => $faker->randomElement(['Yes', 'No']),
                    'AGGREGATION CENTERS/RESPONSE' => $faker->randomElement(['Yes', 'No']),
                    'AGGREGATION CENTERS/SPECIFY' => strtoupper($faker->sentence),
                    'TOTAL AGGREGATION CENTER SALES VOLUME' => $faker->numberBetween(1, 100) * 10,
                ];
            }
        }

        return collect($data);

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
            'APPROACH', // FOR PRODUCER ORGANIZATIONS ONLY
            'SECTOR',
            'NUMBER OF MEMBERS/TOTAL',
            'NUMBER OF MEMBERS/FEMALE 18-35YRS',
            'NUMBER OF MEMBERS/FEMALE 35YRS+',
            'NUMBER OF MEMBERS/MALE 18-35YRS',
            'NUMBER OF MEMBERS/MALE 35YRS+', // FOR PRODUCER ORGANIZATIONS ONLY
            'GROUP',
            'ESTABLISHMENT STATUS', // UPPERCASE FOR ENUM VALUES
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
            'MARKET SEGMENT/Fresh',
            'MARKET SEGMENT/Processed', // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
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
