<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'project_id' => 1
        ]);
    }
}