<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\Organisation;
use Illuminate\Database\Seeder;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Collection;

class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indicators = [
            'Number of actors profitability engaged in commercialization of RTC',
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities',
            'Percentage increase in value of formal RTC exports',
            'Percentage of value ($) of formal RTC imports substituted through local production',
            'Number of people consuming RTC and processed products',
            'Percentage Increase in the volume of RTC produced',
            'Percentage increase in RTC investment',
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion',
            'Number of potential market preferred RTC genotypes in the pipeline identified',
            'Number of new RTC technologies developed',
            'Percentage increase in adoption of new RTC technologies',
            'Number of economic studies conducted',
            'Number of RTC and derived products recorded in official trade statistics',
            'Number of existing agricultural programs that integrate RTC into their programs',
            'Number of policy briefs developed and shared on RTC topics',
            'Number of market linkages between EGS and other seed class producers facilitated',
            'Number of private sector actors involved in production of RTC certified seed',
            'Area (ha) under seed multiplication',
            'Percentage seed multipliers with formal registration',
            'Volume of seed distributed within communities to enhance POs productivity',
            'Number of on-farm seed production technology demonstrations established',
            'Number of international learning visits for seed producers (OC)',
            'Percentage business plans for the production of different classes of RTC seeds that are executed',
            'Number of stakeholder engagement events that focus on RTC development',
            'Number of registered seed producers accessing markets through online Market Information System (MIS)',
            'Number of RTC actors linked to online Market Information System (MIS)',
            'Number of RTC products available on the Management Information System',
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production',
            'Number of RTC actors that use certified seed',
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)',
            'Number of off-season irrigation demonstration sites established',
            'Number of demonstration sites for end-user preferred RTC varieties established',
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)',
            'Number of market opportunities identified for RTC actors',
            'Number of contractual arrangements facilitated for commercial farmers',
            'Number of RTC actors supported to access funds from financial service providers',
            'Number of POs that have formal contracts with buyers',
            'Number of RTC aggregation centers established',
            'Number of RTC POs selling products through aggregation centers',
            'Volume (MT) of RTC products sold through collective marketing efforts by POs',
            'Number of households reached with RTC nutrition interventions',
            'Frequency of RTC consumption by households per week (OC)',
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)',
            'Number of RTC utilization options (dishes) adopted by households (OC)',
            'Number of urban market promotions conducted',
            'Number of mass nutrition education campaigns conducted',
            'Number of RTC value-added products promoted',
            'Number of RTC actors with MBS certification for producing (or processing) RTC products',
            'Number of RTC value-added products developed for domestic markets',
            'Number of new RTC recipes/products adopted and branded by processors',
            'Number of domestic market opportunities identified for value-added products',
            'Number of international market opportunities identified for value-added products',
        ];

        $indicatorNo = [
            'A1',
            'B1',
            'B2',
            'B3',
            'B4',
            'B5',
            'B6',
            '1.1.1',
            '1.1.2',
            '1.1.3',
            '1.1.4',
            '1.2.1',
            '1.2.2',
            '1.3.1',
            '1.3.1',
            '2.1.1',
            '2.2.1',
            '2.2.2',
            '2.2.3',
            '2.2.4',
            '2.2.5',
            '2.3.1',
            '2.3.1',
            '2.3.2',
            '2.3.3',
            '2.3.4',
            '2.3.5',
            '3.1.1',
            '3.2.1',
            '3.2.2',
            '3.2.3',
            '3.2.4',
            '3.2.5',
            '3.3.1',
            '3.3.2',
            '3.4.1',
            '3.4.2',
            '3.4.3',
            '3.4.4',
            '3.4.5',
            '3.5.1',
            '3.5.2',
            '3.5.3',
            '3.5.4',
            '3.5.5',
            '3.5.6',
            '4.1.1',
            '4.1.2',
            '4.1.3',
            '4.1.4',
            '4.1.5',
            '4.1.6',
        ];
        foreach ($indicators as $key => $indicator) {
            Indicator::create([
                'indicator_no' => $indicatorNo[$key],
                'indicator_name' => $indicator,
                'project_id' => 1,
            ]);
        }

        $indicatorsWithPartners = [
            'A1' => ['CIP', 'IITA', 'DAES', 'DCD'],
            'B1' => ['CIP', 'IITA', 'DAES', 'DCD'],
            'B2' => ['MINISTRY OF TRADE', 'CIP'],
            'B3' => ['MINISTRY OF TRADE'],
            'B4' => ['CIP', 'IITA', 'DAES'],
            'B5' => ['CIP', 'IITA', 'DAES'],
            'B6' => ['CIP'],
            '1.1.1' => ['CIP', 'IITA', 'TRADELINE', 'MINISTRY OF TRADE'],
            '1.1.2' => ['DARS'],
            '1.1.3' => ['DARS'],
            '1.1.4' => ['DAES', 'CIP', 'IITA', 'RTCDT', 'DARS'],
            '1.2.1' => ['CIP', 'IITA'],
            '1.2.2' => ['MINISTRY OF TRADE'],
            '1.3.1' => ['RTCDT'],
            '2.1.1' => ['TRADELINE'],
            '2.2.1' => ['CIP', 'IITA'],
            '2.2.2' => ['DAES', 'IITA', 'CIP'],
            '2.2.3' => ['DAES', 'IITA', 'CIP'],
            '2.2.4' => ['DAES', 'CIP', 'IITA'],
            '2.2.5' => ['DAES'],
            '2.3.1' => ['CIP', 'IITA'],
            '2.3.2' => ['TRADELINE', 'RCDT'],
            '2.3.3' => ['ACE'],
            '2.3.4' => ['ACE', 'TRADELINE'],
            '2.3.5' => ['ACE', 'DAES'],
            '3.1.1' => ['DAES', 'CIP', 'IITA'],
            '3.2.1' => ['CIP'],
            '3.2.2' => ['DAES', 'CIP', 'IITA', 'ACE', 'MINISTRY OF TRADE'],
            '3.2.3' => ['DAES'],
            '3.2.4' => ['DAES'],
            '3.2.5' => ['DAES', 'CIP', 'IITA'],
            '3.3.1' => ['TRADELINE'],
            '3.3.2' => ['TRADELINE'],
            '3.4.1' => ['TRADELINE'],
            '3.4.2' => ['TRADELINE'],
            '3.4.3' => ['TRADELINE'],
            '3.4.4' => ['TRADELINE'],
            '3.4.5' => ['TRADELINE'],
            '3.5.1' => ['DAES', 'CIP', 'IITA'],
            '3.5.2' => ['DAES', 'CIP', 'IITA'],
            '3.5.3' => ['DAES', 'CIP', 'IITA'],
            '3.5.4' => ['DAES', 'CIP', 'IITA'],
            '3.5.5' => ['DAES', 'CIP', 'IITA'],
            '3.5.6' => ['DAES'],
            '4.1.1' => ['CIP', 'IITA', 'RTCDT'],
            '4.1.2' => ['RTCDT', 'MINISTRY OF TRADE'],
            '4.1.3' => ['MINISTRY OF TRADE'],
            '4.1.4' => ['CIP', 'IITA', 'DAES'],
            '4.1.5' => ['TRADELINE'],
            '4.1.6' => ['TRADELINE'],
        ];


        $indicatorArray = [
            [
                "id" => 1,
                "indicator_name" => "Number of actors profitability engaged in commercialization of RTC",
                "indicator_no" => "A1",
            ],
            [
                "id" => 2,
                "indicator_name" => "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities",
                "indicator_no" => "B1",
            ],
            [
                "id" => 3,
                "indicator_name" => "Percentage increase in value of formal RTC exports",
                "indicator_no" => "B2",
            ],
            [
                "id" => 4,
                "indicator_name" => "Percentage of value ($) of formal RTC imports substituted through local production",
                "indicator_no" => "B3",
            ],
            [
                "id" => 5,
                "indicator_name" => "Number of people consuming RTC and processed products",
                "indicator_no" => "B4",
            ],
            [
                "id" => 6,
                "indicator_name" => "Percentage Increase in the volume of RTC produced",
                "indicator_no" => "B5",
            ],
            [
                "id" => 7,
                "indicator_name" => "Percentage increase in RTC investment",
                "indicator_no" => "B6",
            ],
            [
                "id" => 8,
                "indicator_name" => "Number of local RTC varieties suitable for domestic and export markets identified for promotion",
                "indicator_no" => "1.1.1",
            ],
            [
                "id" => 9,
                "indicator_name" => "Number of potential market preferred RTC genotypes in the pipeline identified",
                "indicator_no" => "1.1.2",
            ],
            [
                "id" => 10,
                "indicator_name" => "Number of new RTC technologies developed",
                "indicator_no" => "1.1.3",
            ],
            [
                "id" => 11,
                "indicator_name" => "Percentage increase in adoption of new RTC technologies",
                "indicator_no" => "1.1.4",
            ],
            [
                "id" => 12,
                "indicator_name" => "Number of economic studies conducted",
                "indicator_no" => "1.2.1",
            ],
            [
                "id" => 13,
                "indicator_name" => "Number of RTC and derived products recorded in official trade statistics",
                "indicator_no" => "1.2.2",
            ],
            [
                "id" => 14,
                "indicator_name" => "Number of existing agricultural programs that integrate RTC into their programs",
                "indicator_no" => "1.3.1",
            ],
            [
                "id" => 15,
                "indicator_name" => "Number of policy briefs developed and shared on RTC topics",
                "indicator_no" => "1.3.1",
            ],
            [
                "id" => 16,
                "indicator_name" => "Number of market linkages between EGS and other seed class producers facilitated",
                "indicator_no" => "2.1.1",
            ],
            [
                "id" => 17,
                "indicator_name" => "Number of private sector actors involved in production of RTC certified seed",
                "indicator_no" => "2.2.1",
            ],
            [
                "id" => 18,
                "indicator_name" => "Area (ha) under seed multiplication",
                "indicator_no" => "2.2.2",
            ],
            [
                "id" => 19,
                "indicator_name" => "Percentage seed multipliers with formal registration",
                "indicator_no" => "2.2.3",
            ],
            [
                "id" => 20,
                "indicator_name" => "Volume of seed distributed within communities to enhance POs productivity",
                "indicator_no" => "2.2.4",
            ],
            [
                "id" => 21,
                "indicator_name" => "Number of on-farm seed production technology demonstrations established",
                "indicator_no" => "2.2.5",
            ],
            [
                "id" => 22,
                "indicator_name" => "Number of international learning visits for seed producers (OC)",
                "indicator_no" => "2.3.1",
            ],
            [
                "id" => 23,
                "indicator_name" => "Percentage business plans for the production of different classes of RTC seeds that are executed",
                "indicator_no" => "2.3.1",
            ],
            [
                "id" => 24,
                "indicator_name" => "Number of stakeholder engagement events that focus on RTC development",
                "indicator_no" => "2.3.2",
            ],
            [
                "id" => 25,
                "indicator_name" => "Number of registered seed producers accessing markets through online Market Information System (MIS)",
                "indicator_no" => "2.3.3",
            ],
            [
                "id" => 26,
                "indicator_name" => "Number of RTC actors linked to online Market Information System (MIS)",
                "indicator_no" => "2.3.4",
            ],
            [
                "id" => 27,
                "indicator_name" => "Number of RTC products available on the Management Information System",
                "indicator_no" => "2.3.5",
            ],
            [
                "id" => 28,
                "indicator_name" => "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production",
                "indicator_no" => "3.1.1",
            ],
            [
                "id" => 29,
                "indicator_name" => "Number of RTC actors that use certified seed",
                "indicator_no" => "3.2.1",
            ],
            [
                "id" => 30,
                "indicator_name" => "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)",
                "indicator_no" => "3.2.2",
            ],
            [
                "id" => 31,
                "indicator_name" => "Number of off-season irrigation demonstration sites established",
                "indicator_no" => "3.2.3",
            ],
            [
                "id" => 32,
                "indicator_name" => "Number of demonstration sites for end-user preferred RTC varieties established",
                "indicator_no" => "3.2.4",
            ],
            [
                "id" => 33,
                "indicator_name" => "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)",
                "indicator_no" => "3.2.5",
            ],
            [
                "id" => 34,
                "indicator_name" => "Number of market opportunities identified for RTC actors",
                "indicator_no" => "3.3.1",
            ],
            [
                "id" => 35,
                "indicator_name" => "Number of contractual arrangements facilitated for commercial farmers",
                "indicator_no" => "3.3.2",
            ],
            [
                "id" => 36,
                "indicator_name" => "Number of RTC actors supported to access funds from financial service providers",
                "indicator_no" => "3.4.1",
            ],
            [
                "id" => 37,
                "indicator_name" => "Number of POs that have formal contracts with buyers",
                "indicator_no" => "3.4.2",
            ],
            [
                "id" => 38,
                "indicator_name" => "Number of RTC aggregation centers established",
                "indicator_no" => "3.4.3",
            ],
            [
                "id" => 39,
                "indicator_name" => "Number of RTC POs selling products through aggregation centers",
                "indicator_no" => "3.4.4",
            ],
            [
                "id" => 40,
                "indicator_name" => "Volume (MT) of RTC products sold through collective marketing efforts by POs",
                "indicator_no" => "3.4.5",
            ],
            [
                "id" => 41,
                "indicator_name" => "Number of households reached with RTC nutrition interventions",
                "indicator_no" => "3.5.1",
            ],
            [
                "id" => 42,
                "indicator_name" => "Frequency of RTC consumption by households per week (OC)",
                "indicator_no" => "3.5.2",
            ],
            [
                "id" => 43,
                "indicator_name" => "Percentage increase in households consuming RTCs as the main foodstuff (OC)",
                "indicator_no" => "3.5.3",
            ],
            [
                "id" => 44,
                "indicator_name" => "Number of RTC utilization options (dishes) adopted by households (OC)",
                "indicator_no" => "3.5.4",
            ],
            [
                "id" => 45,
                "indicator_name" => "Number of urban market promotions conducted",
                "indicator_no" => "3.5.5",
            ],
            [
                "id" => 46,
                "indicator_name" => "Number of mass nutrition education campaigns conducted",
                "indicator_no" => "3.5.6",
            ],
            [
                "id" => 47,
                "indicator_name" => "Number of RTC value-added products promoted",
                "indicator_no" => "4.1.1",
            ],
            [
                "id" => 48,
                "indicator_name" => "Number of RTC actors with MBS certification for producing (or processing) RTC products",
                "indicator_no" => "4.1.2",
            ],
            [
                "id" => 49,
                "indicator_name" => "Number of RTC value-added products developed for domestic markets",
                "indicator_no" => "4.1.3",
            ],
            [
                "id" => 50,
                "indicator_name" => "Number of new RTC recipes/products adopted and branded by processors",
                "indicator_no" => "4.1.4",
            ],
            [
                "id" => 51,
                "indicator_name" => "Number of domestic market opportunities identified for value-added products",
                "indicator_no" => "4.1.5",
            ],
            [
                "id" => 52,
                "indicator_name" => "Number of international market opportunities identified for value-added products",
                "indicator_no" => "4.1.6",
            ],
        ];


        foreach ($indicatorArray as $indicator) {
            $indicatorIds = Indicator::where('id', $indicator['id'])->pluck('id');

            foreach ($indicatorIds as $indicatorId) {

                $conditions = match ($indicator['indicator_name']) {
                    'Number of actors profitability engaged in commercialization of RTC',
                    'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => [
                        ['names' => ['CIP', 'IITA', 'DAES', 'DCD'], 'type' => 'normal'],
                    ],
                    'Percentage increase in value of formal RTC exports' => [
                        ['names' => ['MINISTRY OF TRADE', 'CIP'], 'type' => 'aggregate'],
                    ],
                    'Percentage of value ($) of formal RTC imports substituted through local production' => [
                        ['names' => ['MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    'Number of people consuming RTC and processed products' => [
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],
                    'Percentage Increase in the volume of RTC produced' => [
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],
                    'Percentage increase in RTC investment' => [
                        ['names' => ['CIP'], 'type' => 'aggregate'],
                    ],
                    'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => [
                        ['names' => ['IITA', 'TRADELINE', 'MINISTRY OF TRADE', 'CIP'], 'type' => 'aggregate'],
                    ],
                    'Number of potential market preferred RTC genotypes in the pipeline identified',
                    'Number of new RTC technologies developed' => [
                        ['names' => ['DARS'], 'type' => 'aggregate'],
                    ],
                    'Percentage increase in adoption of new RTC technologies' => [
                        ['names' => ['IITA', 'DARS', 'RTCDT', 'DAES', 'CIP'], 'type' => 'aggregate'],
                    ],
                    'Number of economic studies conducted' => [
                        ['names' => ['IITA', 'CIP'], 'type' => 'aggregate'],
                    ],
                    'Number of RTC and derived products recorded in official trade statistics' => [
                        ['names' => ['MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    'Number of existing agricultural programs that integrate RTC into their programs', 'Number of policy briefs developed and shared on RTC topics' => [
                        ['names' => ['RTCDT'], 'type' => 'aggregate'],
                    ],


                    'Number of market linkages between EGS and other seed class producers facilitated' => [
                        ['names' => ['TRADELINE'], 'type' => 'aggregate'],
                    ],
                    'Number of private sector actors involved in production of RTC certified seed' => [
                        ['names' => ['CIP', 'IITA'], 'type' => 'normal'],
                    ],
                    'Area (ha) under seed multiplication',
                    'Percentage seed multipliers with formal registration' => [
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],
                    'Volume of seed distributed within communities to enhance POs productivity' => [
                        ['names' => ['CIP', 'DAES', 'IITA'], 'type' => 'normal'],
                    ],
                    'Number of on-farm seed production technology demonstrations established' => [
                        ['names' => ['DAES'], 'type' => 'aggregate'],
                    ],
                    'Number of international learning visits for seed producers (OC)' => [
                        ['names' => ['IITA', 'CIP'], 'type' => 'aggregate'],
                    ],
                    'Percentage business plans for the production of different classes of RTC seeds that are executed' => [
                        ['names' => ['TRADELINE', 'RTCDT'], 'type' => 'aggregate'],
                    ],
                    'Number of stakeholder engagement events that focus on RTC development' => [
                        ['names' => ['ACE'], 'type' => 'aggregate'],
                    ],
                    'Number of registered seed producers accessing markets through online Market Information System (MIS)' => [
                        ['names' => ['ACE', 'TRADELINE'], 'type' => 'aggregate'],
                    ],
                    'Number of RTC actors linked to online Market Information System (MIS)', 'Number of RTC products available on the Management Information System' => [
                        ['names' => ['ACE', 'DAES'], 'type' => 'aggregate'],
                    ],
                    'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => [
                        ['names' => ['CIP', 'DAES', 'IITA'], 'type' => 'normal'],
                    ],
                    'Number of RTC actors that use certified seed' => [
                        ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => [
                        ['names' => ['ACE', 'DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    'Number of off-season irrigation demonstration sites established',
                    'Number of demonstration sites for end-user preferred RTC varieties established' => [
                        ['names' => ['DAES'], 'type' => 'aggregate'],
                    ],
                    'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    'Number of market opportunities identified for RTC actors',
                    'Number of contractual arrangements facilitated for commercial farmers',
                    'Number of RTC actors supported to access funds from financial service providers',
                    'Number of POs that have formal contracts with buyers',
                    'Number of RTC aggregation centers established',
                    'Number of RTC POs selling products through aggregation centers',
                    'Volume (MT) of RTC products sold through collective marketing efforts by POs' => [
                        ['names' => ['TRADELINE'], 'type' => 'aggregate'],
                    ],
                    'Number of households reached with RTC nutrition interventions' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    'Frequency of RTC consumption by households per week (OC)',
                    'Percentage increase in households consuming RTCs as the main foodstuff (OC)',
                    'Number of RTC utilization options (dishes) adopted by households (OC)' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'normal'],
                    ],
                    'Number of urban market promotions conducted' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    'Number of mass nutrition education campaigns conducted' => [
                        ['names' => ['DAES'], 'type' => 'aggregate'],
                    ],
                    'Number of RTC value-added products promoted' => [
                        ['names' => ['RTCDT', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    'Number of RTC actors with MBS certification for producing (or processing) RTC products' => [
                        ['names' => ['RTCDT', 'MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    'Number of RTC value-added products developed for domestic markets' => [
                        ['names' => ['MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    'Number of new RTC recipes/products adopted and branded by processors' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    'Number of domestic market opportunities identified for value-added products',
                    'Number of international market opportunities identified for value-added products' => [
                        ['names' => ['TRADELINE'], 'type' => 'aggregate'],
                    ],
                    default => []
                };


                foreach ($conditions as $condition) {

                    foreach ($condition['names'] as $name) {
                        $organisationIds = Organisation::where('name', $name)->pluck('id');

                        foreach ($organisationIds as $organisationId) {


                            ResponsiblePerson::create([
                                'indicator_id' => $indicatorId,
                                'organisation_id' => $organisationId,
                                'type_of_submission' => $condition['type'],
                                // 'aggregate_type' => $condition['type'] === 'aggregate' ? 'disaggregation' : null,
                            ]);

                            // }
                        }
                    }
                }
            }
        }


    }
}
