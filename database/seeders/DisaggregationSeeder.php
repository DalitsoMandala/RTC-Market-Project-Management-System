<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use Illuminate\Database\Seeder;

class DisaggregationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $disaggregations = [
            'A1' => ['Total', 'Female', 'Male', 'Youth (18-35 yrs)', 'Not youth (35yrs+)', 'Farmers', 'Processors', 'Traders', 'Employees on RTC establishment', 'Cassava', 'Potato', 'Sweet potato', 'New establishment', 'Old establishment'],
        ];

        foreach ($disaggregations['A1'] as $data) {
            $indicator = Indicator::where('indicator_no', 'A1')->first();
            IndicatorDisaggregation::create([
                'name' => $data,
                'indicator_id' => $indicator->id,
            ]);

        }

    }
}
