<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\IndicatorSubTarget;
use App\Models\IndicatorTarget;
use Illuminate\Database\Seeder;

class IndicatorTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $indicators = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 60000],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 30],
            'Percentage increase in value of formal RTC exports' => ['Total' => 14],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 30],
            'Number of people consuming RTC and processed products' => ['Total' => 1000000],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 40],
            'Percentage increase in RTC investment' => ['Total' => 15],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 16],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 11],
            'Number of new RTC technologies developed' => ['Total' => 12],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 20],
            'Number of economic studies conducted' => ['Total' => 4],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 7],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 9],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 7],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 29],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 0], // target value not provided
            'Area (ha) under seed multiplication' => ['Total' => 617],
            'Percentage seed multipliers with formal registration' => ['Total' => 10],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => '100 tons'],
                'Sweetpotato' => ['Total' => '102,000 bundles'],
                'Cassava' => ['Total' => '33,000 bundles'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 2],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 37],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 700],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 40000],
            'Number of RTC products available on the Management Information System' => ['Total' => 24],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 52],
            'Number of RTC actors that use certified seed' => ['Total' => 100],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 947],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 65],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 120],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 25],
            'Number of market opportunities identified for RTC actors' => ['Total' => 41],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 23],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 36],
            'Number of POs that have formal contracts with buyers' => ['Total' => 20],
            'Number of RTC aggregation centers established' => ['Total' => 7],
            'Number of RTC POs selling products through aggregation centers aggregation center' => ['Total' => 0], // target value not provided
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => '25,000'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 58000],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 4],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => '29%'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 6],
            'Number of urban market promotions conducted' => ['Total' => 8],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 24],
            'Number of RTC value-added products promoted' => ['Total' => 0], // target value not provided
            'Number of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 12],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 10],
            'Number of new RTC recipes/products adopted and branded by processors' => ['Total' => 6],
            'Number of domestic market opportunities identified for value-added products' => ['Total' => 3],
            'Number of international market opportunities identified for value-added products' => ['Total' => 2],
        ];

        // By financial year

        $year1 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 10000],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 0],
            'Percentage increase in value of formal RTC exports' => ['Total' => 5],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 0],
            'Number of people consuming RTC and processed products' => ['Total' => 100000],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 5],
            'Percentage increase in RTC investment' => ['Total' => 0],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 6],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 2],
            'Number of new RTC technologies developed' => ['Total' => 2],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 5],
            'Number of economic studies conducted' => ['Total' => 1],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 3],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 3],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 1],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 5],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 2],
            'Area (ha) under seed multiplication' => ['Total' => 107],
            'Percentage seed multipliers with formal registration' => ['Total' => 5],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => '10 tons'],
                'Sweetpotato' => ['Total' => '12000 bundles'],
                'Cassava' => ['Total' => '3000 bundles'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 1],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 8],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 100],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000],
            'Number of RTC products available on the Management Information System' => ['Total' => 6],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 12],
            'Number of RTC actors that use certified seed' => ['Total' => 50],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 180],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 10],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 5],
            'Number of market opportunities identified for RTC actors' => ['Total' => 8],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 5],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 9],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5],
            'Number of RTC aggregation centers established' => ['Total' => 1],
            'Percentage of RTC POs selling produce through aggregation center' => ['Total' => 3],
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => '3,000'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 8000],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 3],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => 5],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 3],
            'Number of urban market promotions conducted' => ['Total' => 2],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 6],
            'Number of RTC value-added products promoted' => ['Total' => 2],
            'Percentage of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 6],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 6],
            'Number of new recipes/products adopted and branded by processors' => ['Total' => 6],
            'Number of domestic market opportunities for processed products identified' => ['Total' => 3],
            'Number of export market opportunities for processed products identified' => ['Total' => 1],
        ];

        $year2 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 15000],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 10],
            'Percentage increase in value of formal RTC exports' => ['Total' => 3],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 5],
            'Number of people consuming RTC and processed products' => ['Total' => 250000],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 10],
            'Percentage increase in RTC investment' => ['Total' => 5],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 6],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 3],
            'Number of new RTC technologies developed' => ['Total' => 3],
            'Percentage increase in adoption of new RTC technologies' => ['Total' => 10],
            'Number of economic studies conducted' => ['Total' => 1],
            'Number of RTC and derived products recorded in official trade statistics' => ['Total' => 1],
            'Number of existing agricultural programs that integrate RTC into their programs' => ['Total' => 2],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 2],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 6],
            'Number of private sector actors involved in production of RTC certified seed' => ['Total' => 2],
            'Area (ha) under seed multiplication' => ['Total' => 130],
            'Percentage seed multipliers with formal registration' => ['Total' => 10],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => '20 tons'],
                'Sweetpotato' => ['Total' => '20000 bundles'],
                'Cassava' => ['Total' => '5000 bundles'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 1],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 9],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 150],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000],
            'Number of RTC products available on the Management Information System' => ['Total' => 6],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 15],
            'Number of RTC actors that use certified seed' => ['Total' => 80],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 246],
            'Number of off-season irrigation demonstration sites established' => ['Total' => 15],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30],
            'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 10],
            'Number of market opportunities identified for RTC actors' => ['Total' => 10],
            'Number of contractual arrangements facilitated for commercial farmers' => ['Total' => 6],
            'Number of RTC actors supported to access funds from financial service providers' => ['Total' => 9],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5],
            'Number of RTC aggregation centers established' => ['Total' => 1],
            'Percentage of RTC POs selling produce through aggregation center' => ['Total' => 7],
            'Volume (MT) of RTC products sold through collective marketing efforts by POs' => ['Total' => '6,000'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 12000],
            'Frequency of RTC consumption by households per week (OC)' => ['Total' => 4],
            'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => ['Total' => 8],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 5],
            'Number of urban market promotions conducted' => ['Total' => 2],
            'Number of mass nutrition education campaigns conducted' => ['Total' => 6],
            'Number of RTC value-added products promoted' => ['Total' => 2],
            'Percentage of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 6],
            'Number of RTC value-added products developed for domestic markets' => ['Total' => 8],
            'Number of new recipes/products adopted and branded by processors' => ['Total' => 2],
            'Number of domestic market opportunities for processed products identified' => ['Total' => 3],
            'Number of export market opportunities for processed products identified' => ['Total' => 1],
        ];

        $year3 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 15000],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 15],
            'Percentage increase in value of formal RTC exports' => ['Total' => 3],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 20],
            'Number of people consuming RTC and processed products' => ['Total' => 300000],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 15],
            'Percentage increase in RTC investment' => ['Total' => 5],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 2],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 3],
            'Number of new RTC technologies developed' => ['Total' => 4],
            'Percentage increase in adoption of improved RTC technologies' => ['Total' => 15],
            'Number of economic studies conducted' => ['Total' => 1],
            'Number of RTC and derived products officially recorded in trade statistics' => ['Total' => 2],
            'Percentage of existing agricultural programs that integrate RTC into their programs' => ['Total' => 2],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 2],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 8],
            'Number of private sector involved in production of RTC certified seed' => ['Total' => 2],
            'Area (ha) under seed multiplication' => ['Total' => 170],
            'Percentage seed multipliers with formal registration' => ['Total' => '10%'],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => '30 ton'],
                'Sweetpotato' => ['Total' => '30000 Bundles'],
                'Cassava' => ['Total' => '10000 Bundles'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 0],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 9],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 200],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000],
            'Number of RTC products available on the Management Information System' => ['Total' => 6],
            'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 15],
            'Number of RTC actors that use certified seed' => ['Total' => 80],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => ['Total' => 255],
            'Number of off-season irrigation demo-sites established' => ['Total' => 20],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30],
            'Percentage increase in irrigated off season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 15],
            'Number of market opportunities identified for RTC actors' => ['Total' => 11],
            'Number of contractual arrangements facilitated between and among commercial farmers' => ['Total' => 6],
            'Number of RTC actors that are supported to access funds from financial service providers' => ['Total' => 9],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5],
            'Number of RTC aggregation centers established' => ['Total' => 3],
            'Percentage of RTC POs selling produce through aggregation center' => ['Total' => 10],
            'Volume (MT) of RTC sold through collective marketing efforts by POs' => ['Total' => '8000'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 18000],
            'Frequency (days) of RTC consumption by households per week (OC)' => ['Total' => 4],
            'Percentage increase in households consuming RTCs as main foodstuff (OC)' => ['Total' => '8%'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 6],
            'Number of urban market promotions' => ['Total' => 2],
            'Number of mass nutrition education campaigns' => ['Total' => 6],
            'Number of RTC value added products promoted' => ['Total' => 2],
            'Percentage of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 7],
            'Number of RTC value added products on domestic market developed' => ['Total' => 8],
            'Number of new recipes/products adopted and branded by processors' => ['Total' => 2],
            'Number of domestic market opportunities for processed products identified' => ['Total' => 3],
            'Number of export market opportunities for processed products identified' => ['Total' => 1],
        ];

        $year4 = [
            'Number of actors profitability engaged in commercialization of RTC' => ['Total' => 20000],
            'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => ['Total' => 30],
            'Percentage increase in value of formal RTC exports' => ['Total' => 3],
            'Percentage of value ($) of formal RTC imports substituted through local production' => ['Total' => 30],
            'Number of people consuming RTC and processed products' => ['Total' => 350000],
            'Percentage Increase in the volume of RTC produced' => ['Total' => 10],
            'Percentage increase in RTC investment' => ['Total' => 5],
            'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => ['Total' => 2],
            'Number of potential market preferred RTC genotypes in the pipeline identified' => ['Total' => 3],
            'Number of improved RTC technologies developed' => ['Total' => 3],
            'Percentage increase in adoption of improved RTC technologies' => ['Total' => 20],
            'Number of economic studies conducted' => ['Total' => 1],
            'Number of RTC and derived products officially recorded in trade statistics' => ['Total' => 1],
            'Percentage of existing agricultural programs that integrate RTC into their programs' => ['Total' => 2],
            'Number of policy briefs developed and shared on RTC topics' => ['Total' => 2],
            'Number of market linkages between EGS and other seed class producers facilitated' => ['Total' => 10],
            'Number of private sector involved in production of RTC certified seed' => ['Total' => 1],
            'Area (ha) under seed multiplication' => ['Total' => 210],
            'Percentage seed multipliers with formal registration' => ['Total' => '10%'],
            'Volume of seed distributed within communities to enhance POs productivity' => [
                'Potato' => ['Total' => '40 ton'],
                'Sweetpotato' => ['Total' => '40000 Bundles'],
                'Cassava' => ['Total' => '15000 Bundles'],
            ],
            'Number of on-farm seed production technology demonstrations established' => ['Total' => 6],
            'Number of international learning visits for seed producers (OC)' => ['Total' => 0],
            'Percentage business plans for the production of different classes of RTC seeds that are executed' => ['Total' => 9],
            'Number of stakeholder engagement events that focus on RTC development' => ['Total' => 11],
            'Number of registered seed producers accessing markets through online Market Information System (MIS)' => ['Total' => 250],
            'Number of RTC actors linked to online Market Information System (MIS)' => ['Total' => 10000],
            'Number of RTC products available on the Management Information System' => ['Total' => 6],
            'Number of Large scale producer organisations (POs) and private sector commercial farms involved in RTC production' => ['Total' => 10],
            'Number of RTC actors that use certified seed' => ['Total' => 100],
            'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc)' => ['Total' => 266],
            'Number of off-season irrigation demo-sites established' => ['Total' => 20],
            'Number of demonstration sites for end-user preferred RTC varieties established' => ['Total' => 30],
            'Percentage increase in irrigated off season RTC production by POs and commercial farmers (from baseline)' => ['Total' => 25],
            'Number of market opportunities identified for RTC actors' => ['Total' => 12],
            'Number of contractual arrangements facilitated between and among commercial farmers' => ['Total' => 6],
            'Number of RTC actors that are supported to access funds from financial service providers' => ['Total' => 9],
            'Number of POs that have formal contracts with buyers' => ['Total' => 5],
            'Number of RTC aggregation centers established' => ['Total' => 2],
            'Percentage of RTC POs selling produce through aggregation center' => ['Total' => 20],
            'Volume (MT) of RTC sold through collective marketing efforts by POs' => ['Total' => '8000'],
            'Number of households reached with RTC nutrition interventions' => ['Total' => 20000],
            'Frequency (days) of RTC consumption by households per week (OC)' => ['Total' => 4],
            'Percentage increase in households consuming RTCs as main foodstuff (OC)' => ['Total' => '10%'],
            'Number of RTC utilization options (dishes) adopted by households (OC)' => ['Total' => 6],
            'Number of urban market promotions' => ['Total' => 2],
            'Number of mass nutrition education campaigns' => ['Total' => 6],
            'Number of RTC value added products promoted' => ['Total' => 2],
            'Percentage of RTC actors with MBS certification for producing (or processing) RTC products' => ['Total' => 8],
            'Number of RTC value added products on domestic market developed' => ['Total' => 10],
            'Number of new recipes/products adopted and branded by processors' => ['Total' => 2],
            'Number of domestic market opportunities for processed products identified' => ['Total' => 3],
            'Number of export market opportunities for processed products identified' => ['Total' => 2],
        ];

        foreach ($indicators as $indicator => $values) {
            $getIndicatorId = Indicator::where('indicator_name', $indicator)->first();

            if ($getIndicatorId) {

                $target = IndicatorTarget::insert([
                    'indicator_id' => $getIndicatorId->id,
                    'target' => json_encode($values),

                ]);

            }

        }

        $targets = IndicatorTarget::get(); // Assuming this retrieves your targets
        $allyears = [$year1, $year2, $year3, $year4]; // Array of years

        //yearone
        $firstYearArray = array_values($allyears[0]);
        foreach ($targets as $key => $target) {

            foreach ($firstYearArray as $fkey => $firstArray) {
                if ($fkey == $key) {
                    IndicatorSubTarget::create([
                        'financial_year_id' => 1,
                        'target' => json_encode($firstArray),
                        'indicator_target_id' => $target->id,
                    ]);

                }

            }
        }

        //yeartwo
        $firstYearArray = array_values($allyears[1]);
        foreach ($targets as $key => $target) {

            foreach ($firstYearArray as $fkey => $firstArray) {
                if ($fkey == $key) {
                    IndicatorSubTarget::create([
                        'financial_year_id' => 2,
                        'target' => json_encode($firstArray),
                        'indicator_target_id' => $target->id,
                    ]);

                }

            }
        }

        //yearthree
        $firstYearArray = array_values($allyears[2]);
        foreach ($targets as $key => $target) {

            foreach ($firstYearArray as $fkey => $firstArray) {
                if ($fkey == $key) {
                    IndicatorSubTarget::create([
                        'financial_year_id' => 3,
                        'target' => json_encode($firstArray),
                        'indicator_target_id' => $target->id,
                    ]);

                }

            }
        }

        //yearfour
        $firstYearArray = array_values($allyears[3]);
        foreach ($targets as $key => $target) {

            foreach ($firstYearArray as $fkey => $firstArray) {
                if ($fkey == $key) {
                    IndicatorSubTarget::create([
                        'financial_year_id' => 4,
                        'target' => json_encode($firstArray),
                        'indicator_target_id' => $target->id,
                    ]);

                }

            }
        }

        // foreach ($targets as $key => $target) {
        //     foreach ($allyears as $yearKey => $yearCount) {
        //         $financial_year_id = $yearKey + 1; // Calculate financial year ID (1 to 4)

        //         // Assuming $yearCount is an array of values you want to JSON encode
        //         $values = $allyears[$yearKey]; // Get values for the current year
        //         $encodedValues = json_encode($values);

        //         // Create IndicatorSubTarget entry
        //         IndicatorSubTarget::create([
        //             'financial_year_id' => $financial_year_id,
        //             'target' => $encodedValues,
        //             'indicator_target_id' => $target->id,
        //         ]);
        //     }
        // }

    }
}
