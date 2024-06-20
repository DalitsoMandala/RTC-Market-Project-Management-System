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
            '2.3.1' => ['CIP/IITA', 'CIP/ IITA'], // Assuming it's meant to be two separate entries
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
        foreach ($indicatorsWithPartners as $indicator => $partners) {
            $indicatorId = Indicator::where('indicator_no', $indicator)->pluck('id')->first();
            $responsiblePeopleArray[$indicator] = [
                'organisation_id' => Organisation::whereIn('name', $partners)->pluck('id')->toArray(),
                'organisation_names' => Organisation::whereIn('name', $partners)->pluck('name')->toArray(),
                'indicator_id' => $indicatorId,
            ];

        }

        //if values exist
        function allValuesInArray($values, $array)
        {
            foreach ($values as $value) {
                if (!in_array($value, $array)) {
                    return false;
                }
            }
            return true;
        }

        foreach ($responsiblePeopleArray as $indicator_no => $res) {

            switch ($indicator_no) {

                case 'A1':

                    if (allValuesInArray(['CIP', 'IITA', 'DAES', 'DCD'], $res['organisation_names'])) {

                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,

                            ]);
                        }

                    }
                    break;

                case 'B1':

                    if (allValuesInArray(['CIP', 'IITA', 'DAES', 'DCD'], $res['organisation_names'])) {

                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,

                            ]);
                        }

                    }
                    break;
                case 'B2':

                    if (allValuesInArray(['MINISTRY OF TRADE', 'CIP'], $res['organisation_names'])) {

                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }

                    }
                    break;

                case 'B3':
                    if (allValuesInArray(['MINISTRY OF TRADE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case 'B4':
                    if (allValuesInArray(['IITA', 'DAES'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }

                    if (allValuesInArray(['CIP'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,

                            ]);
                        }
                    }
                    break;

                case 'B5':
                    if (allValuesInArray(['CIP', 'IITA'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case 'B6':
                    if (allValuesInArray(['CIP'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '1.1.1':
                    if (allValuesInArray(['CIP', 'IITA', 'TRADELINE', 'MINISTRY OF TRADE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '1.1.2':
                case '1.1.3':
                    if (allValuesInArray(['DARS'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '1.1.4':
                    if (allValuesInArray(['CIP', 'IITA', 'DARS', 'RTCDT', 'DAES'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '1.2.1':
                    if (allValuesInArray(['CIP', 'IITA'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '1.2.2':
                    if (allValuesInArray(['MINISTRY OF TRADE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '1.3.1':
                    if (allValuesInArray(['RTCDT'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.1.1':
                    if (allValuesInArray(['TRADELINE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.2.1':
                    if (allValuesInArray(['CIP', 'IITA'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.2.2':
                case '2.2.3':
                case '2.2.4':
                    if (allValuesInArray(['DAES', 'CIP', 'IITA'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.2.5':
                    if (allValuesInArray(['DAES'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.3.1':
                    if (allValuesInArray(['CIP', 'IITA'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.3.2':
                    if (allValuesInArray(['TRADELINE', 'RTCDT'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.3.3':
                    if (allValuesInArray(['ACE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.3.4':
                    if (allValuesInArray(['ACE', 'TRADELINE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '2.3.5':
                    if (allValuesInArray(['ACE', 'DAES'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '3.1.1':
                    if (allValuesInArray(['DAES', 'CIP', 'IITA', 'RTCDT'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '3.1.2':
                    if (allValuesInArray(['CIP', 'IITA', 'RTCDT'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                case '3.1.3':
                    if (allValuesInArray(['ACE', 'DAES', 'TRADELINE'], $res['organisation_names'])) {
                        foreach ($res['organisation_id'] as $organisationId) {
                            ResponsiblePerson::create([
                                'indicator_id' => $res['indicator_id'],
                                'organisation_id' => $organisationId,
                                'type_of_submission' => 'aggregate',
                            ]);
                        }
                    }
                    break;

                default:
                    // Handle default case if needed
                    break;

            }

        }

    }
}
