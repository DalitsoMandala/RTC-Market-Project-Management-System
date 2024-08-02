<?php

namespace Database\Seeders;

use App\Helpers\IndicatorsContent;
use App\Models\Indicator;
use App\Models\IndicatorClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndicatorClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $classes = IndicatorsContent::indicatorCalculations();
        foreach ($classes as $class) {

            $indicator = Indicator::where('indicator_name', $class['indicator_name'])->first();
            if ($indicator) {
                IndicatorClass::create([
                    'indicator_id' => $indicator->id,
                    'class' => $class['class']
                ]);

            }


            # code...
        }
    }
}
