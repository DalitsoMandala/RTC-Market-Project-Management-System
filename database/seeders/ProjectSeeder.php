<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Project::create([
            'name' => 'RTC Market',
            'duration' => 4,  // years
            'start_date' => '2024-06-18',
            'cgiar_project_id' => 1
        ]);
    }
}