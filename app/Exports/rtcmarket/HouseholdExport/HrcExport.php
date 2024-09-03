<?php

namespace App\Exports\rtcmarket\HouseholdExport;

use App\Helpers\ArrayToUpperCase;
use App\Helpers\Help;
use App\Helpers\ToUpper;
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
        $rand = rand(100, 1000);

        if ($this->test) {
            foreach (range(1, 10) as $index) {

                $data[] = [
                    'ENTERPRISE' => $faker->randomElement(['Cassava', 'Potato', 'Sweet potato']),
                    'DISTRICT' => $faker->randomElement(
                        [
                            'Balaka',
                            'Blantyre',
                            'Chikwawa',
                            'Chiradzulu',
                            'Chitipa',
                            'Dedza',
                            'Dowa',
                            'Karonga',
                            'Kasungu',
                            'Lilongwe',
                            'Machinga',
                            'Mangochi',
                            'Mchinji',
                            'Mulanje',
                            'Mwanza',
                            'Mzimba',
                            'Neno',
                            'Nkhata Bay',
                            'Nkhotakota',
                            'Nsanje',
                            'Ntcheu',
                            'Ntchisi',
                            'Phalombe',
                            'Rumphi',
                            'Salima',
                            'Thyolo',
                            'Zomba',
                        ]
                    ),
                    'EPA' => $faker->randomElement($epaNames),
                    'SECTION' => $faker->randomElement($sectionNames),
                    'DATE OF ASSESSMENT' => $faker->date('Y-m-d'),
                    'ACTOR TYPE' => $faker->randomElement(['Farmer', 'Processor', 'Trader', 'Individuals from nutrition intervention', 'Other']),
                    'RTC GROUP PLATFORM' => $faker->randomElement(['Household', 'Seed']),
                    'PRODUCER ORGANISATION' => $faker->randomElement($organisationNames),
                    'ACTOR NAME' => strtoupper($faker->name),
                    'AGE GROUP' => $faker->randomElement(['Youth', 'Not youth']),
                    'SEX' => $faker->randomElement(['Male', 'Female']),
                    'PHONE NUMBER' => $faker->numerify('###-###-####'),
                    'HOUSEHOLD SIZE' => $faker->numberBetween(1, 100),
                    'UNDER 5 IN HOUSEHOLD' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS/POTATO' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS/SWEET POTATO' => $faker->numberBetween(1, 100),
                    'RTC CONSUMERS/CASSAVA' => $faker->numberBetween(1, 100),
                    'RTC CONSUMPTION FREQUENCY' => $faker->numberBetween(1, 100),
                    'RTC MAIN FOOD/CASSAVA' => $faker->randomElement(['Cassava', '']),
                    'RTC MAIN FOOD/POTATO' => $faker->randomElement(['Potato', '']),
                    'RTC MAIN FOOD/SWEET POTATO' => $faker->randomElement(['Sweet potato', '']),

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
