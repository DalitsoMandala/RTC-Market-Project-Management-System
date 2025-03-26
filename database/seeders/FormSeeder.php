<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Indicator;
use Illuminate\Database\Seeder;
use App\Helpers\IndicatorsContent;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $forms = [
            //'HOUSEHOLD CONSUMPTION FORM',
            'RTC ACTOR RECRUITMENT FORM',
            'RTC CONSUMPTION FORM',
            'RTC PRODUCTION AND MARKETING FORM FARMERS',
            'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS',
            'REPORT FORM',
            'ATTENDANCE REGISTER',
            'SEED DISTRIBUTION REGISTER',


        ];

        foreach ($forms as $formName) {
            Form::firstOrCreate([
                'name' => $formName,
                'type' => 'routine/recurring',
                'project_id' => 1,
                'slug' => strtolower(str_replace(' ', '-', $formName))
            ]);
        }
        $indicators = IndicatorsContent::indicatorArray()->pluck('indicator_name', 'indicator_name');

        // Mappings using the indicator names directly
        $indicatorMappings = [
            $indicators['Number of actors profitability engaged in commercialization of RTC'] => [
                //  'RTC CONSUMPTION FORM',
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS',
                'RTC ACTOR RECRUITMENT FORM',
            ],
            $indicators['Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS',

            ],
            $indicators['Percentage increase in value of formal RTC exports'] => ['REPORT FORM'],
            $indicators['Percentage of value ($) of formal RTC imports substituted through local production'] => ['REPORT FORM'],
            $indicators['Number of people consuming RTC and processed products'] => [
                //  'HOUSEHOLD CONSUMPTION FORM',
                'RTC CONSUMPTION FORM'
            ],
            $indicators['Percentage Increase in the volume of RTC produced'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Percentage increase in RTC investment'] => ['REPORT FORM'],
            $indicators['Number of local RTC varieties suitable for domestic and export markets identified for promotion'] => ['REPORT FORM'],
            $indicators['Number of potential market preferred RTC genotypes in the pipeline identified'] => ['REPORT FORM'],
            $indicators['Number of new RTC technologies developed'] => ['REPORT FORM'],
            $indicators['Percentage increase in adoption of new RTC technologies'] => ['REPORT FORM'],
            $indicators['Number of economic studies conducted'] => ['REPORT FORM'],
            $indicators['Number of RTC and derived products recorded in official trade statistics'] => ['REPORT FORM'],
            $indicators['Number of existing agricultural programs that integrate RTC into their programs'] => ['REPORT FORM'],
            $indicators['Number of policy briefs developed and shared on RTC topics'] => ['REPORT FORM'],
            $indicators['Number of market linkages between EGS and other seed class producers facilitated'] => ['REPORT FORM'],
            $indicators['Number of private sector actors involved in production of RTC certified seed'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Area (ha) under seed multiplication'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Percentage seed multipliers with formal registration'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Volume of seed distributed within communities to enhance POs productivity'] => ['SEED DISTRIBUTION REGISTER'],
            $indicators['Number of on-farm seed production technology demonstrations established'] => ['REPORT FORM'],
            $indicators['Number of international learning visits for seed producers (OC)'] => ['REPORT FORM'],
            $indicators['Percentage business plans for the production of different classes of RTC seeds that are executed'] => ['REPORT FORM'],
            $indicators['Number of stakeholder engagement events that focus on RTC development'] => ['REPORT FORM'],
            $indicators['Number of registered seed producers accessing markets through online Market Information System (MIS)'] => [
                'REPORT FORM',
                // 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Number of RTC actors linked to online Market Information System (MIS)'] => ['REPORT FORM'],
            $indicators['Number of RTC products available on the Management Information System'] => ['REPORT FORM'],
            $indicators['Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Number of RTC actors that use certified seed'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)'] => ['ATTENDANCE REGISTER'],
            $indicators['Number of off-season irrigation demonstration sites established'] => ['REPORT FORM'],
            $indicators['Number of demonstration sites for end-user preferred RTC varieties established'] => ['REPORT FORM'],
            //  $indicators['Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)'] => ['RTC PRODUCTION AND MARKETING FORM FARMERS'],
            $indicators['Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)'] => [
                'RTC PRODUCTION AND MARKETING FORM FARMERS',
                'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
            ],
            $indicators['Number of market opportunities identified for RTC actors'] => ['REPORT FORM'],
            $indicators['Number of contractual arrangements facilitated for commercial farmers'] => ['REPORT FORM'],
            $indicators['Number of RTC actors supported to access funds from financial service providers'] => ['REPORT FORM'],
            $indicators['Number of POs that have formal contracts with buyers'] => [
                //  'RTC PRODUCTION AND MARKETING FORM FARMERS',
                //  'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
                'REPORT FORM'
            ],
            $indicators['Number of RTC aggregation centers established'] => ['REPORT FORM'],
            $indicators['Number of RTC POs selling products through aggregation centers'] => [
                //'RTC PRODUCTION AND MARKETING FORM FARMERS',
                // 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
                'REPORT FORM'
            ],
            $indicators['Volume (MT) of RTC products sold through collective marketing efforts by POs'] => [
                // 'RTC PRODUCTION AND MARKETING FORM FARMERS',
                // 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS'
                'REPORT FORM'
            ],
            $indicators['Number of households reached with RTC nutrition interventions'] => ['REPORT FORM'],
            $indicators['Frequency of RTC consumption by households per week (OC)'] => ['HOUSEHOLD CONSUMPTION FORM'],
            $indicators['Percentage increase in households consuming RTCs as the main foodstuff (OC)'] => ['HOUSEHOLD CONSUMPTION FORM'],
            $indicators['Number of RTC utilization options (dishes) adopted by households (OC)'] => ['REPORT FORM'],
            $indicators['Number of urban market promotions conducted'] => ['REPORT FORM'],
            $indicators['Number of mass nutrition education campaigns conducted'] => ['REPORT FORM'],
            $indicators['Number of RTC value-added products promoted'] => ['REPORT FORM'],
            $indicators['Number of RTC actors with MBS certification for producing (or processing) RTC products'] => [
                'REPORT FORM'
            ],
            $indicators['Number of RTC value-added products developed for domestic markets'] => ['REPORT FORM'],
            $indicators['Number of new RTC recipes/products adopted and branded by processors'] => ['REPORT FORM'],
            $indicators['Number of domestic market opportunities identified for value-added products'] => ['REPORT FORM'],
            $indicators['Number of international market opportunities identified for value-added products'] => ['REPORT FORM'],
        ];

        foreach ($indicatorMappings as $indicatorName => $formNames) {
            $indicators = Indicator::where('indicator_name', $indicatorName)->get();
            if ($indicators) {
                foreach ($indicators as $indicator) {

                    $formIds = Form::whereIn('name', $formNames)->pluck('id');
                    $indicator->forms()->attach($formIds);
                }
            }
        }
    }
}
