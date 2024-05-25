<?php

namespace Database\Seeders;

use App\Models\Form;
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
            'project_id' => 1,
            'responsible_people' => [

            ],
        ]);
        Form::create([
            'name' => 'RTC PRODUCTION AND MARKETING FORM FARMERS',
            'type' => 'routine/reccurring',
            'project_id' => 1,
            'responsible_people' => [

            ],
        ]);
        Form::create([
            'name' => 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
            'type' => 'routine/reccurring',
            'project_id' => 1,
            'responsible_people' => [

            ],
        ]);
        Form::create([
            'name' => 'SCHOOL RTC CONSUMPTION FORM',
            'type' => 'routine/reccurring',
            'project_id' => 1,
            'responsible_people' => [

            ],
        ]);

    }
}
