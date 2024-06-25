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
            'start_date' => '2023-05-01',
            'cgiar_project_id' => 1,
            'is_active' => true,
            'reporting_period_id' => 1,
        ]);

        $startDate = Carbon::create(2023, 5, 1);

        foreach (range(1, $project->duration) as $index) {
            // Calculate the start date for the financial year
            $yearStartDate = $startDate->copy()->addYears($index - 1);

            // Calculate the end date for the financial year (April 30 of the next year)
            $yearEndDate = $yearStartDate->copy()->addYear()->month(4)->endOfMonth();

            FinancialYear::create([
                'number' => $index,
                'project_id' => $project->id,
                'start_date' => $yearStartDate->format('Y-m-d'),
                'end_date' => $yearEndDate->format('Y-m-d'),
            ]);
        }
    }
}