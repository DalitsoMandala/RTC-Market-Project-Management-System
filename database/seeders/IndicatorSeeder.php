<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use Illuminate\Database\Seeder;

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
            'Number of RTC POs selling products through aggregation centers aggregation center',
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

        $responsiblePeopleArray = [];
        foreach ($indicatorNo as $indicator) {
            $indicatorIds = Indicator::where('indicator_no', $indicator)->pluck('id');
            foreach ($indicatorIds as $indicatorId) {
                //  $organisationIds = Organisation::whereIn('name', $partners)->pluck('id')->toArray();

                $conditions = match ($indicator) {
                    'A1', 'B1' => [
                        ['names' => ['CIP', 'IITA', 'DAES', 'DCD'], 'type' => 'normal'],
                    ], //done
                    'B2' => [
                        ['names' => ['MINISTRY OF TRADE', 'CIP'], 'type' => 'aggregate'],
                        //  ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    'B3' => [
                        ['names' => ['MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    'B4' => [
                        //  ['names' => ['IITA', 'DAES',], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],
                    'B5' => [
                        //  ['names' => ['IITA'], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],
                    'B6' => [
                        ['names' => ['CIP'], 'type' => 'aggregate'],
                    ],
                    '1.1.1' => [
                        ['names' => ['IITA', 'TRADELINE', 'MINISTRY OF TRADE', 'CIP'], 'type' => 'aggregate'],
                        // ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    '1.1.2', '1.1.3' => [
                        ['names' => ['DARS'], 'type' => 'aggregate'],
                    ],
                    '1.1.4' => [
                        ['names' => ['IITA', 'DARS', 'RTCDT', 'DAES', 'CIP'], 'type' => 'aggregate'],
                        // ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    '1.2.1' => [
                        ['names' => ['IITA', 'CIP'], 'type' => 'aggregate'],
                        //  ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    '1.2.2' => [
                        ['names' => ['MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    '1.3.1' => [
                        ['names' => ['RTCDT'], 'type' => 'aggregate'],
                    ],

                    '2.1.1' => [
                        ['names' => ['TRADELINE'], 'type' => 'aggregate'],
                    ],
                    '2.2.1' => [
                        // ['names' => ['IITA'], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'IITA'], 'type' => 'normal'],
                    ],
                    '2.2.2' => [
                        //   ['names' => ['DAES', 'IITA'], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],

                    '2.2.3' => [
                        //   ['names' => ['DAES', 'IITA'], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'IITA', 'DAES'], 'type' => 'normal'],
                    ],
                    '2.2.4' => [
                        // ['names' => ['DAES', 'IITA'], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'DAES', 'IITA'], 'type' => 'normal'],
                    ],
                    '2.2.5' => [
                        ['names' => ['DAES'], 'type' => 'aggregate'],
                    ],
                    '2.3.1' => [
                        ['names' => ['IITA', 'CIP'], 'type' => 'aggregate'],
                        //  ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    '2.3.2' => [
                        ['names' => ['TRADELINE', 'RTCDT'], 'type' => 'aggregate'],
                    ],
                    '2.3.3' => [
                        ['names' => ['ACE'], 'type' => 'aggregate'],
                    ],
                    '2.3.4' => [
                        ['names' => ['ACE', 'TRADELINE'], 'type' => 'aggregate'],
                    ],
                    '2.3.5' => [
                        ['names' => ['ACE', 'DAES'], 'type' => 'aggregate'],
                    ],
                    '3.1.1' => [
                        // ['names' => ['DAES', 'IITA', 'RTCDT'], 'type' => 'aggregate'],
                        ['names' => ['CIP', 'DAES', 'IITA'], 'type' => 'normal'],
                    ],
                    '3.2.1' => [
                        //  ['names' => ['IITA', 'RTCDT'], 'type' => 'aggregate'],
                        ['names' => ['CIP'], 'type' => 'normal'],
                    ],
                    '3.2.2' => [
                        ['names' => ['ACE', 'DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],

                    '3.2.3', '3.2.4' => [
                        ['names' => ['DAES'], 'type' => 'aggregate'],
                    ],

                    '3.2.5' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],

                    '3.3.1', '3.3.2', '3.4.1', '3.4.2', '3.4.3', '3.4.4', '3.4.5' => [
                        ['names' => ['TRADELINE'], 'type' => 'aggregate'],
                    ],

                    '3.5.1' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    '3.5.2', '3.5.3', '3.5.4' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'normal'],
                    ],

                    '3.5.5' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],

                    '3.5.6' => [
                        ['names' => ['DAES'], 'type' => 'aggregate'],
                    ],

                    '4.1.1' => [
                        ['names' => ['RTCDT', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],

                    '4.1.2' => [
                        ['names' => ['RTCDT', 'MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],

                    '4.1.3' => [
                        ['names' => ['MINISTRY OF TRADE'], 'type' => 'aggregate'],
                    ],
                    '4.1.4' => [
                        ['names' => ['DAES', 'CIP', 'IITA'], 'type' => 'aggregate'],
                    ],
                    '4.1.5', '4.1.6' => [
                        ['names' => ['TRADELINE'], 'type' => 'aggregate'],
                    ],
                    default => []
                };

                foreach ($conditions as $condition) {

                    foreach ($condition['names'] as $name) {
                        $organisationIds = Organisation::where('name', $name)->pluck('id');

                        foreach ($organisationIds as $organisationId) {
                            // if (isset($condition['aggregate_type'])) {
                            //     ResponsiblePerson::create([
                            //         'indicator_id' => $indicatorId,
                            //         'organisation_id' => $organisationId,
                            //         'type_of_submission' => $condition['type'],
                            //         'aggregate_type' => $condition['aggregate_type'],
                            //     ]);
                            // } else {

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
