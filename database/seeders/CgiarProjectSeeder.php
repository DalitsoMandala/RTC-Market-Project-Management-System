<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cgiar_Project;

class CgiarProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Cgiar_Project::create([
            'name' => 'International Potato Center',
            'slug' => 'cip',

        ]);
        Cgiar_Project::create([
            'name' => 'Development Smart Innovation through Research in Agriculture',
            'slug' => 'desira',

        ]);
    }
}