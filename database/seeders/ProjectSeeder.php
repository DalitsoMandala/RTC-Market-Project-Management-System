<?php

namespace Database\Seeders;

use App\Models\FinancialYear;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $project = Project::create([
            'name' => 'RTC MARKET',
            'duration' => 4, // years
            'start_date' => '2023-05-18',
            'cgiar_project_id' => 1,
            'is_active' => true,
            'reporting_period_id' => 1,
        ]);

        $startDate = Carbon::create(2023, 5, 18);

        foreach (range(1, $project->duration) as $index) {
            $yearStartDate = $startDate->copy()->addYears($index - 1);
            $yearEndDate = $yearStartDate->copy()->addYear()->subDay(); // End date is one day before the next year's start date

            FinancialYear::create([
                'number' => $index,
                'project_id' => $project->id,
                'start_date' => $yearStartDate->format('Y-m-d'),
                'end_date' => $yearEndDate->format('Y-m-d'),
            ]);
        }
    }
}
