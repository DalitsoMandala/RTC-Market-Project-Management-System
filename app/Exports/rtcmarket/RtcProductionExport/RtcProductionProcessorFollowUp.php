<?php

namespace App\Exports\rtcmarket\RtcProductionExport;

use App\Helpers\Help;
use Faker\Factory as Faker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RtcProductionProcessorFollowUp implements FromCollection, WithTitle, WithHeadings
{
    public $test = false;

    public function __construct($test = false)
    {

        $this->test = $test;
    }
    public function title(): string
    {
        return 'RTC_PROC_FLUP';
    }
    public function collection()
    {

        $faker = Faker::create();
        $data = [];

        if ($this->test) {
            foreach (range(1, 20) as $index) {
                $data[] = [
                    'RECRUIT ID' => $faker->numberBetween(1, 10),
                    'ENTERPRISE' => $faker->randomElement(['CASSAVA', 'POTATO', 'SWEET POTATO']),
                    'GROUP NAME' => strtoupper($faker->name),
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
                    'EPA' => $faker->randomElement(Help::getFakerNames()['epaNames']),
                    'SECTION' => $faker->randomElement(Help::getFakerNames()['sectionNames']),
                    'DATE OF FOLLOW UP' => now(),
                    'MARKET SEGMENT/FRESH' => $faker->randomElement(['YES', 'NO']),
                    'MARKET SEGMENT/PROCESSED' => $faker->randomElement(['YES', 'NO']), // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
                    'HAS RTC MARKET CONTRACT' => $faker->randomElement(['YES', 'NO']),
                    'TOTAL VOLUME PRODUCTION PREVIOUS SEASON' => $faker->optional()->numberBetween(1, 100) * 10,
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES' => $faker->date('Y-m-d'),
                    'TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON' => $faker->optional()->randomFloat(2, 1, 100),
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL' => $faker->numberBetween(1, 100) * 10,
                    'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES' => $faker->date('Y-m-d'),
                    'SELLS TO DOMESTIC MARKETS' => $faker->randomElement(['YES', 'NO']),
                    'SELLS TO INTERNATIONAL MARKETS' => $faker->randomElement(['YES', 'NO']),
                    'USES MARKET INFORMATION SYSTEMS' => $faker->randomElement(['YES', 'NO']),
                    'MARKET INFORMATION SYSTEMS' => strtoupper($faker->text),
                    'SELLS TO AGGREGATION CENTERS' => $faker->randomElement(['YES', 'NO']),
                    'AGGREGATION CENTERS/RESPONSE' => $faker->randomElement(['YES', 'NO']),
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
            'RECRUIT ID',
            'ENTERPRISE',
            'GROUP NAME',
            'DISTRICT',
            'EPA',
            'SECTION',
            'DATE OF FOLLOW UP',
            'MARKET SEGMENT/FRESH',
            'MARKET SEGMENT/PROCESSED', // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
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
