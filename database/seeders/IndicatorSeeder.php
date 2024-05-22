<?php

namespace Database\Seeders;

use App\Models\Indicator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

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
            'Number ofRTC products available on the Management Information System',
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
            '4.1.6'
        ];
        foreach ($indicators as $key => $indicator) {
            Indicator::create([
                'indicator_no' => $indicatorNo[$key],
                'indicator_name' => $indicator,
                'project_id' => 1
            ]);
        }
    }
}