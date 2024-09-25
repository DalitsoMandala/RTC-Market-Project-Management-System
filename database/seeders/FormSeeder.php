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
            // 'RTC INFORMAL EXPORT REGISTER FORM',
            // 'EXPORT AND IMPORT MATRIX FORM',
            'ATTENDANCE REGISTER',
            'REPORT FORM',
            //  'SEED DISTRIBUTION REGISTER',
        ];

        // Create forms
        foreach ($forms as $formName) {
            Form::create([
                'name' => $formName,
                'type' => 'routine/recurring',
                'project_id' => 1,
            ]);
        }

        $indicatorMappings = [
            'A1' => ['HOUSEHOLD CONSUMPTION FORM', 'RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            'B1' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS', 'ATTENDANCE REGISTER'],
            'B2' => ['REPORT FORM'],
            'B3' => ['REPORT FORM'],
            'B4' => ['HOUSEHOLD CONSUMPTION FORM', 'SCHOOL RTC CONSUMPTION FORM'],
            'B5' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            'B6' => ['REPORT FORM'],
            '1.1.1' => ['REPORT FORM'],
            '1.1.2' => ['REPORT FORM'],
            '1.1.3' => ['REPORT FORM'],
            '1.1.4' => ['REPORT FORM'],
            '1.2.1' => ['REPORT FORM'],
            '1.2.2' => ['REPORT FORM'],
            '1.3.1' => ['REPORT FORM'],
            '2.1.1' => ['REPORT FORM'],
            '2.2.1' => ['REPORT FORM'],
            '2.2.2' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '2.2.3' => ['REPORT FORM'],
            '2.2.4' => ['REPORT FORM'],
            '2.2.5' => ['REPORT FORM'],
            '2.3.1' => ['REPORT FORM'],
            '2.3.2' => ['REPORT FORM'],
            '2.3.3' => ['REPORT FORM'],
            '2.3.4' => ['REPORT FORM'],
            '2.3.5' => ['REPORT FORM'],
            '3.1.1' => ['RTC PRODUCTION AND MARKETING FORM FARMERS', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS'],
            '3.2.1' => ['REPORT FORM'],
            '3.2.2' => ['REPORT FORM'],
            '3.2.3' => ['REPORT FORM'],
            '3.2.4' => ['REPORT FORM'],
            '3.2.5' => ['REPORT FORM'],
            '3.3.1' => ['REPORT FORM'],
            '3.3.2' => ['REPORT FORM'],
            '3.4.1' => ['REPORT FORM'],
            '3.4.2' => ['REPORT FORM'],
            '3.4.3' => ['REPORT FORM'],
            '3.4.4' => ['REPORT FORM'],
            '3.4.5' => ['REPORT FORM'],
            '3.5.1' => ['REPORT FORM'],
            '3.5.2' => ['HOUSEHOLD CONSUMPTION FORM'],
            '3.5.3' => ['HOUSEHOLD CONSUMPTION FORM'],
            '3.5.4' => ['HOUSEHOLD CONSUMPTION FORM'],
            '3.5.5' => ['REPORT FORM'],
            '3.5.6' => ['REPORT FORM'],
            '4.1.1' => ['REPORT FORM'],
            '4.1.2' => ['REPORT FORM'],
            '4.1.3' => ['REPORT FORM'],
            '4.1.4' => ['REPORT FORM'],
            '4.1.5' => ['REPORT FORM'],
            '4.1.6' => ['REPORT FORM'],
        ];
        foreach ($indicatorMappings as $indicatorNumber => $formNames) {
            $indicators = Indicator::where('indicator_no', $indicatorNumber)->get();
            if ($indicators) {
                foreach ($indicators as $indicator) {

                    $formIds = Form::whereIn('name', $formNames)->pluck('id');
                    $indicator->forms()->attach($formIds);

                }
            }

        }

    }

}
