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

        Form::create([
            'name' => 'HOUSEHOLD CONSUMPTION FORM',
            'type' => 'routine/reccurring',
            'project_id' => 1, // RTC MARKET

        ]);
        Form::create([
            'name' => 'RTC PRODUCTION AND MARKETING FORM FARMERS',
            'type' => 'routine/reccurring',
            'project_id' => 1,

        ]);
        Form::create([
            'name' => 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
            'type' => 'routine/reccurring',
            'project_id' => 1,

        ]);
        Form::create([
            'name' => 'SCHOOL RTC CONSUMPTION FORM',
            'type' => 'routine/reccurring',
            'project_id' => 1,

        ]);

        $indicators = Indicator::where('indicator_name', 'Number of actors profitability engaged in commercialization of RTC')->first();
        if ($indicators) {
            $formIds = [];
            $forms1 = Form::where('name', 'HOUSEHOLD CONSUMPTION FORM')
                ->OrWhere('name', 'RTC PRODUCTION AND MARKETING FORM FARMERS')
                ->OrWhere('name', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS')
                ->get()->pluck('id');

            $formIds = $forms1;
            $indicators->forms()->attach($formIds);

        }

        // set forms involved with this indicator

    }
}
