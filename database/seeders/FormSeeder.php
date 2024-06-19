<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Indicator;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $forms = [
            'HOUSEHOLD CONSUMPTION FORM',
            'RTC PRODUCTION AND MARKETING FORM FARMERS',
            'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
            'SCHOOL RTC CONSUMPTION FORM',
            'RTC INFORMAL EXPORT REGISTER FORM',
            'EXPORT AND IMPORT MATRIX FORM',
            'ATTENDANCE REGISTER',
        ];

        // Create forms
        foreach ($forms as $formName) {
            Form::create([
                'name' => $formName,
                'type' => 'routine/recurring',
                'project_id' => 1,
            ]);
        }

        // // Indicator-form mappings
        // $indicatorMappings = [
        //     'Number of actors profitability engaged in commercialization of RTC' => [
        //         'HOUSEHOLD CONSUMPTION FORM',
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS',
        //         'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS',
        //         'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Number of private sector actors involved in production of RTC certified seed' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Area (ha) under seed multiplication' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Percentage seed multipliers with formal registration' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Number of registered seed producers accessing markets through online Market Information System (MIS)' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Percentage increase in irrigated off season RTC production by POs and commercial farmers (from baseline)' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS',
        //     ],
        //     'Number of POs that have formal contracts with buyers' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Number of RTC POs selling products through aggregation centers' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Volume (MT) of RTC products sold through collective marketing efforts by POs' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Number of RTC actors with MBS certification for producing (or processing) RTC products' => [
        //         'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
        //     ],
        //     'Frequency of RTC consumption by households per week (OC)' => [
        //         'HOUSEHOLD CONSUMPTION FORM',
        //     ],
        //     'Percentage increase in households consuming RTCs as main food stuff (OC)' => [
        //         'HOUSEHOLD CONSUMPTION FORM',
        //     ],
        //     'Number of RTC utilization options (dishes) adopted by households (OC)' => [
        //         'SCHOOL RTC CONSUMPTION FORM',
        //     ],
        // ];

        $indicatorMappings = [
            'A1' => ['HOUSEHOLD CONSUMPTION FORM', 'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            'B1' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            'B2' => ['RTC INFORMAL EXPORT REGISTER'],
            'B3' => ['EXPORT AND IMPORT MATRIX FORM'],
            'B4' => ['HOUSEHOLD CONSUMPTION FORM', 'SCHOOL RTC CONSUMPTION FORM'],
            'B5' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '1.2.2' => ['EXPORT AND IMPORT MATRIX FORM'],
            '2.2.1' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '2.2.2' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '2.2.3' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '2.3.4' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.1.1' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.2.2' => ['ATTENDANCE REGISTER'],
            '3.2.5' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.4.2' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.4.4' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.4.5' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.5.2' => ['HOUSEHOLD CONSUMPTION FORM', 'SCHOOL RTC CONSUMPTION FORM'],
            '3.5.3' => ['HOUSEHOLD CONSUMPTION FORM'],
            '3.5.4' => ['SCHOOL RTC CONSUMPTION FORM'],
            '4.1.2' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
        ];
        foreach ($indicatorMappings as $indicatorNumber => $formNames) {
            $indicator = Indicator::where('indicator_no', $indicatorNumber)->first();
            if ($indicator) {
                $formIds = Form::whereIn('name', $formNames)->pluck('id');
                $indicator->forms()->attach($formIds);
            }
        }

    }

}
