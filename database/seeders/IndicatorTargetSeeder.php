<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\IndicatorSubTarget;
use App\Models\IndicatorTarget;
use App\Models\TargetDetail;
use Illuminate\Database\Seeder;

class IndicatorTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // By financial year

        $year1 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 10000, 'type' => 'number'],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 0, 'type' => 'percentage'],
            'Percentage increase in value of formal RTC exports' => ['Total' => 5, 'type' => 'percentage'],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 0, 'type' => 'percentage'],
            'Number of people consuming RTC and processed products' => ['Total' => 100000, 'type' => 'number'],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 5, 'type' => 'percentage'],
            'Percentage increase in RTC investment' => ['Total' => 0, 'type' => 'percentage'],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 6, 'type' => 'number'],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 2, 'type' => 'number'],
            'Number of new RTC technologies developed' => ['Total' => 2, 'type' => 'number'],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 5, 'type' => 'percentage'],
            'Number of economic studies conducted' => ['Total' => 1, 'type' => 'number'],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 3, 'type' => 'number'],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 3, 'type' => 'number'],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 1, 'type' => 'number'],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 5, 'type' => 'number'],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 2, 'type' => 'number'],
            'Area (ha) under seed multiplication' => ['Total' => 107, 'type' => 'number'],
            'Percentage seed multipliers with formal registration' => ['Total' => 5, 'type' => 'percentage'],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => 10, 'type' => 'number'],
                'Sweetpotato' => ['Total' => 12000, 'type' => 'number'],
                'Cassava' => ['Total' => 3000, 'type' => 'number'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6, 'type' => 'number'],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 1, 'type' => 'number'],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9, 'type' => 'percentage'],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 8, 'type' => 'number'],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 100, 'type' => 'number'],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000, 'type' => 'number'],
            'Number of RTC products available on the Management Information System' => ['Total' => 6, 'type' => 'number'],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 12, 'type' => 'number'],
            'Number of RTC actors that use certified seed' => ['Total' => 50, 'type' => 'number'],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 180, 'type' => 'number'],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 10, 'type' => 'number'],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30, 'type' => 'number'],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 5, 'type' => 'percentage'],
            'Number of market opportunities identified for RTC actors' => ['Total' => 8, 'type' => 'number'],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 5, 'type' => 'number'],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 9, 'type' => 'number'],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5, 'type' => 'number'],
            'Number of RTC aggregation centers established' => ['Total' => 1, 'type' => 'number'],
            'Number of RTC POs selling products through aggregation centers' => ['Total' => 3, 'type' => 'number'],
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => 3000, 'type' => 'percentage'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 8000, 'type' => 'number'],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 3, 'type' => 'number'],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => 5, 'type' => 'percentage'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 3, 'type' => 'number'],
            'Number of urban market promotions conducted' => ['Total' => 2, 'type' => 'number'],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC value-added products promoted' => ['Total' => 2, 'type' => 'number'],
            'Number of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 6, 'type' => 'number'],
            'Number of new RTC recipes/products adopted and branded by processors' => ['Total' => 6, 'type' => 'number'],
            'Number of domestic market opportunities identified for value-added products' => ['Total' => 3, 'type' => 'number'],
            'Number of international market opportunities identified for value-added products' => ['Total' => 1, 'type' => 'number'],
        ];



        $year2 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 15000, 'type' => 'number'],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 10, 'type' => 'percentage'],
            'Percentage increase in value of formal RTC exports' => ['Total' => 3, 'type' => 'percentage'],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 5, 'type' => 'percentage'],
            'Number of people consuming RTC and processed products' => ['Total' => 250000, 'type' => 'number'],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 10, 'type' => 'percentage'],
            'Percentage increase in RTC investment' => ['Total' => 5, 'type' => 'percentage'],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 6, 'type' => 'number'],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 3, 'type' => 'number'],
            'Number of new RTC technologies developed' => ['Total' => 3, 'type' => 'number'],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 10, 'type' => 'percentage'],
            'Number of economic studies conducted' => ['Total' => 1, 'type' => 'number'],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 1, 'type' => 'number'],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 2, 'type' => 'number'],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 2, 'type' => 'number'],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 6, 'type' => 'number'],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 2, 'type' => 'number'],
            'Area (ha) under seed multiplication' => ['Total' => 130, 'type' => 'number'],
            'Percentage seed multipliers with formal registration' => ['Total' => 10, 'type' => 'percentage'],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => 20, 'type' => 'number'],
                'Sweetpotato' => ['Total' => 20000, 'type' => 'number'],
                'Cassava' => ['Total' => 5000, 'type' => 'number'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6, 'type' => 'number'],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 1, 'type' => 'number'],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9, 'type' => 'percentage'],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 9, 'type' => 'number'],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 150, 'type' => 'number'],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000, 'type' => 'number'],
            'Number of RTC products available on the Management Information System' => ['Total' => 6, 'type' => 'number'],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 15, 'type' => 'number'],
            'Number of RTC actors that use certified seed' => ['Total' => 80, 'type' => 'number'],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 246, 'type' => 'number'],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 15, 'type' => 'number'],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30, 'type' => 'number'],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 10, 'type' => 'percentage'],
            'Number of market opportunities identified for RTC actors' => ['Total' => 10, 'type' => 'number'],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 9, 'type' => 'number'],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5, 'type' => 'number'],
            'Number of RTC aggregation centers established' => ['Total' => 1, 'type' => 'number'],
            'Number of RTC POs selling products through aggregation centers' => ['Total' => 7, 'type' => 'number'],
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => 6000, 'type' => 'number'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 12000, 'type' => 'number'],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 4, 'type' => 'number'],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => 8, 'type' => 'percentage'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 5, 'type' => 'number'],
            'Number of urban market promotions conducted' => ['Total' => 2, 'type' => 'number'],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC value-added products promoted' => ['Total' => 2, 'type' => 'number'],
            'Number of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 8, 'type' => 'number'],
            'Number of new RTC recipes/products adopted and branded by processors' => ['Total' => 2, 'type' => 'number'],
            'Number of domestic market opportunities identified for value-added products' => ['Total' => 3, 'type' => 'number'],
            'Number of international market opportunities identified for value-added products' => ['Total' => 1, 'type' => 'number'],
        ];

        $year3 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 15000, 'type' => 'number'],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 15, 'type' => 'percentage'],
            'Percentage increase in value of formal RTC exports' => ['Total' => 3, 'type' => 'percentage'],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 20, 'type' => 'percentage'],
            'Number of people consuming RTC and processed products' => ['Total' => 300000, 'type' => 'number'],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 15, 'type' => 'percentage'],
            'Percentage increase in RTC investment' => ['Total' => 5, 'type' => 'percentage'],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 2, 'type' => 'number'],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 3, 'type' => 'number'],
            'Number of new RTC technologies developed' => ['Total' => 4, 'type' => 'number'],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 15, 'type' => 'percentage'],
            'Number of economic studies conducted' => ['Total' => 1, 'type' => 'number'],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 2, 'type' => 'percentage'],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 2, 'type' => 'number'],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 2, 'type' => 'number'],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 8, 'type' => 'number'],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 2, 'type' => 'number'],
            'Area (ha) under seed multiplication' => ['Total' => 170, 'type' => 'number'],
            'Percentage seed multipliers with formal registration' => ['Total' => 10, 'type' => 'percentage'],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => 30, 'type' => 'number'],
                'Sweetpotato' => ['Total' => 30000, 'type' => 'number'],
                'Cassava' => ['Total' => 10000, 'type' => 'number'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6, 'type' => 'number'],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 0, 'type' => 'number'],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9, 'type' => 'percentage'],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 9, 'type' => 'number'],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 200, 'type' => 'number'],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000, 'type' => 'number'],
            'Number of RTC products available on the Management Information System' => ['Total' => 6, 'type' => 'number'],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 15, 'type' => 'number'],
            'Number of RTC actors that use certified seed' => ['Total' => 80, 'type' => 'number'],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 255, 'type' => 'number'],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 20, 'type' => 'number'],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30, 'type' => 'number'],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 15, 'type' => 'percentage'],
            'Number of market opportunities identified for RTC actors' => ['Total' => 11, 'type' => 'number'],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 9, 'type' => 'number'],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5, 'type' => 'number'],
            'Number of RTC aggregation centers established' => ['Total' => 3, 'type' => 'number'],
            'Number of RTC POs selling products through aggregation centers' => ['Total' => 10, 'type' => 'number'],
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => '8000', 'type' => 'number'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 18000, 'type' => 'number'],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 4, 'type' => 'number'],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => 8, 'type' => 'percentage'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 6, 'type' => 'number'],
            'Number of urban market promotions conducted' => ['Total' => 2, 'type' => 'number'],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC value-added products promoted' => ['Total' => 2, 'type' => 'number'],
            'Number of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 7, 'type' => 'number'],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 8, 'type' => 'number'],
            'Number of new RTC recipes/products adopted and branded by processors' => ['Total' => 2, 'type' => 'number'],
            'Number of domestic market opportunities identified for value-added products' => ['Total' => 3, 'type' => 'number'],
            'Number of international market opportunities identified for value-added products' => ['Total' => 1, 'type' => 'number'],
        ];

        $year4 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 20000, 'type' => 'number'],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 30, 'type' => 'percentage'],
            'Percentage increase in value of formal RTC exports' => ['Total' => 3, 'type' => 'percentage'],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 30, 'type' => 'percentage'],
            'Number of people consuming RTC and processed products' => ['Total' => 350000, 'type' => 'number'],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 10, 'type' => 'percentage'],
            'Percentage increase in RTC investment' => ['Total' => 5, 'type' => 'percentage'],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 2, 'type' => 'number'],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 3, 'type' => 'number'],
            'Number of new RTC technologies developed' => ['Total' => 3, 'type' => 'number'],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 20, 'type' => 'percentage'],
            'Number of economic studies conducted' => ['Total' => 1, 'type' => 'number'],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 1, 'type' => 'number'],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 2, 'type' => 'number'],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 2, 'type' => 'number'],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 10, 'type' => 'number'],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 1, 'type' => 'number'],
            'Area (ha) under seed multiplication' => ['Total' => 210, 'type' => 'number'],
            'Percentage seed multipliers with formal registration' => ['Total' => 10, 'type' => 'percentage'],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => 40, 'type' => 'number'],
                'Sweetpotato' => ['Total' => 40000, 'type' => 'number'],
                'Cassava' => ['Total' => 15000, 'type' => 'number'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6, 'type' => 'number'],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 0, 'type' => 'number'],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9, 'type' => 'percentage'],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 11, 'type' => 'number'],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 250, 'type' => 'number'],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000, 'type' => 'number'],
            'Number of RTC products available on the Management Information System' => ['Total' => 6, 'type' => 'number'],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 10, 'type' => 'number'],
            'Number of RTC actors that use certified seed' => ['Total' => 100, 'type' => 'number'],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 266, 'type' => 'number'],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 20, 'type' => 'number'],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30, 'type' => 'number'],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 25, 'type' => 'percentage'],
            'Number of market opportunities identified for RTC actors' => ['Total' => 12, 'type' => 'number'],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 9, 'type' => 'number'],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5, 'type' => 'number'],
            'Number of RTC aggregation centers established' => ['Total' => 2, 'type' => 'number'],
            'Number of RTC POs selling products through aggregation centers' => ['Total' => 20, 'type' => 'number'],
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => '8000', 'type' => 'number'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 20000, 'type' => 'number'],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 4, 'type' => 'number'],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => 10, 'type' => 'percentage'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 6, 'type' => 'number'],
            'Number of urban market promotions conducted' => ['Total' => 2, 'type' => 'number'],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 6, 'type' => 'number'],
            'Number of RTC value-added products promoted' => ['Total' => 2, 'type' => 'number'],
            'Number of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 8, 'type' => 'number'],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 10, 'type' => 'number'],
            'Number of new RTC recipes/products adopted and branded by processors' => ['Total' => 2, 'type' => 'number'],
            'Number of domestic market opportunities identified for value-added products' => ['Total' => 3, 'type' => 'number'],
            'Number of international market opportunities identified for value-added products' => ['Total' => 2, 'type' => 'number'],
        ];


        foreach ($year1 as $indicator => $values) {
            $getIndicatorId = Indicator::where('indicator_name', $indicator)->first();

            if ($getIndicatorId) {

                $target = IndicatorTarget::create([
                    'indicator_id' => $getIndicatorId->id,
                    'target_value' => isset($values['Total']) ? $values['Total'] : null,
                    'project_id' => 1,
                    'financial_year_id' => 1,
                    'type' => isset($values['Total']) ? $values['type'] : 'detail'


                ]);

                //assign target

                if (!isset($values['Total'])) {


                    foreach ($values as $name => $value) {

                        TargetDetail::create([
                            'indicator_target_id' => $target->id,
                            'target_value' => $value['Total'],
                            'name' => $name,
                            'type' => $value['type'],
                        ]);
                    }

                }

            }

        }


        foreach ($year2 as $indicator => $values) {
            $getIndicatorId = Indicator::where('indicator_name', $indicator)->first();

            if ($getIndicatorId) {

                $target = IndicatorTarget::create([
                    'indicator_id' => $getIndicatorId->id,
                    'target_value' => isset($values['Total']) ? $values['Total'] : null,
                    'project_id' => 1,
                    'financial_year_id' => 2,
                    'type' => isset($values['Total']) ? $values['type'] : 'detail'


                ]);




                if (!isset($values['Total'])) {


                    foreach ($values as $name => $value) {

                        TargetDetail::create([
                            'indicator_target_id' => $target->id,
                            'target_value' => $value['Total'],
                            'name' => $name,
                            'type' => $value['type'],
                        ]);
                    }

                }

            }

        }

        foreach ($year3 as $indicator => $values) {
            $getIndicatorId = Indicator::where('indicator_name', $indicator)->first();

            if ($getIndicatorId) {

                $target = IndicatorTarget::create([
                    'indicator_id' => $getIndicatorId->id,
                    'target_value' => isset($values['Total']) ? $values['Total'] : null,
                    'project_id' => 1,
                    'financial_year_id' => 3,
                    'type' => isset($values['Total']) ? $values['type'] : 'detail'


                ]);

                if (!isset($values['Total'])) {


                    foreach ($values as $name => $value) {

                        TargetDetail::create([
                            'indicator_target_id' => $target->id,
                            'target_value' => $value['Total'],
                            'name' => $name,
                            'type' => $value['type'],
                        ]);
                    }

                }

            }

        }

        foreach ($year4 as $indicator => $values) {
            $getIndicatorId = Indicator::where('indicator_name', $indicator)->first();

            if ($getIndicatorId) {

                $target = IndicatorTarget::create([
                    'indicator_id' => $getIndicatorId->id,
                    'target_value' => isset($values['Total']) ? $values['Total'] : null,
                    'project_id' => 1,
                    'financial_year_id' => 4,
                    'type' => isset($values['Total']) ? $values['type'] : 'detail'


                ]);

                if (!isset($values['Total'])) {


                    foreach ($values as $name => $value) {

                        TargetDetail::create([
                            'indicator_target_id' => $target->id,
                            'target_value' => $value['Total'],
                            'name' => $name,
                            'type' => $value['type'],
                        ]);
                    }

                }

            }

        }


    }


}
