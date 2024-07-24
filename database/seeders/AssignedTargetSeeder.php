<?php

namespace Database\Seeders;

use App\Models\AssignedTarget;
use App\Models\IndicatorTarget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignedTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $target = IndicatorTarget::find(53);

        if ($target) {
            AssignedTarget::create([
                'indicator_target_id' => $target->id,
                'organisation_id' => 1,
                'target_value' => 3000,
                'current_value' => 0,
                'type' => 'number',

            ]);
            AssignedTarget::create([
                'indicator_target_id' => $target->id,
                'organisation_id' => 3,
                'target_value' => 2000,
                'type' => 'number',
                'current_value' => 0,
            ]);

            AssignedTarget::create([
                'indicator_target_id' => $target->id,
                'organisation_id' => 4,
                'target_value' => 1000,
                'type' => 'number',
                'current_value' => 0,

            ]);
            AssignedTarget::create([
                'indicator_target_id' => $target->id,
                'organisation_id' => 5,
                'target_value' => 4000,
                'type' => 'number',
                'current_value' => 0,
            ]);

        }

    }
}
