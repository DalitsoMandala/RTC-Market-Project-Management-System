<?php

namespace Database\Seeders;

use App\Models\Baseline;
use App\Models\Indicator;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BaselineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //



        foreach (Indicator::all() as $indicator) {
            $faker = Faker::create();
            Baseline::create([
                'indicator_id' => $indicator->id,
                'baseline_value' => 0
            ]);
        }




    }
}