<?php

namespace Database\Seeders;

use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'is_open' => true
        ]);
    }
}