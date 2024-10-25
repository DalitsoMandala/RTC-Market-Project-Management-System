<?php

namespace Database\Seeders;

use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $dateEstablished = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');  // Today's date
        $dateEnding = Carbon::now()->addMonth()->startOfDay()->format('Y-m-d H:i:s');  // Date one month from today

        DB::statement("
    INSERT INTO cdms.submission_periods(id, form_id, date_established, date_ending, month_range_period_id, financial_year_id, indicator_id, is_open, is_expired, created_at, updated_at) VALUES
    (1, 1, '$dateEstablished', '$dateEnding', 1, 1, 1, 1, 0, '$dateEstablished', '$dateEstablished'),
    (2, 2, '$dateEstablished', '$dateEnding', 1, 1, 1, 1, 0, '$dateEstablished', '$dateEstablished'),
    (3, 3, '$dateEstablished', '$dateEnding', 1, 1, 1, 1, 0, '$dateEstablished', '$dateEstablished')
");
    }
}
