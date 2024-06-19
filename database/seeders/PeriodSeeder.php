<?php

namespace Database\Seeders;

use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        SubmissionPeriod::create([
            'form_id' => 1,
            'date_established' => now(),
            'date_ending' => Carbon::tomorrow(),
            'financial_year_id' => 1,
            'month_range_period_id' => 1,
            'indicator_id' => 1,
            'is_open' => true,
        ]);
    }
}
