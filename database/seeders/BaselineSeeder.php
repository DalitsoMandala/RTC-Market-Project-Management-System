<?php

namespace Database\Seeders;

use App\Models\Baseline;
use App\Models\BaselineDataMultiple;
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

        $data = [
            ['id' => 1, 'indicator_id' => 1, 'baseline_value' => 1783.00, 'baseline_is_multiple' => 0],
            ['id' => 2, 'indicator_id' => 2, 'baseline_value' => 477455.82, 'baseline_is_multiple' => 0],
            ['id' => 3, 'indicator_id' => 3, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 4, 'indicator_id' => 4, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 5, 'indicator_id' => 5, 'baseline_value' => 15640.00, 'baseline_is_multiple' => 0],
            ['id' => 6, 'indicator_id' => 6, 'baseline_value' => 6516.00, 'baseline_is_multiple' => 0],
            ['id' => 7, 'indicator_id' => 7, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 8, 'indicator_id' => 8, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 9, 'indicator_id' => 9, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 10, 'indicator_id' => 10, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 11, 'indicator_id' => 11, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 12, 'indicator_id' => 12, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 13, 'indicator_id' => 13, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 14, 'indicator_id' => 14, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 15, 'indicator_id' => 15, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 16, 'indicator_id' => 16, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 17, 'indicator_id' => 17, 'baseline_value' => 10.00, 'baseline_is_multiple' => 0],
            ['id' => 18, 'indicator_id' => 18, 'baseline_value' => 100.00, 'baseline_is_multiple' => 0],
            ['id' => 19, 'indicator_id' => 19, 'baseline_value' => 10.00, 'baseline_is_multiple' => 0],
            ['id' => 20, 'indicator_id' => 20, 'baseline_value' => 0.00, 'baseline_is_multiple' => 1],
            ['id' => 21, 'indicator_id' => 21, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 22, 'indicator_id' => 22, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 23, 'indicator_id' => 23, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 24, 'indicator_id' => 24, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 25, 'indicator_id' => 25, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 26, 'indicator_id' => 26, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 27, 'indicator_id' => 27, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 28, 'indicator_id' => 28, 'baseline_value' => 132.00, 'baseline_is_multiple' => 0],
            ['id' => 29, 'indicator_id' => 29, 'baseline_value' => 112.00, 'baseline_is_multiple' => 0],
            ['id' => 30, 'indicator_id' => 30, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 31, 'indicator_id' => 31, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 32, 'indicator_id' => 32, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 33, 'indicator_id' => 33, 'baseline_value' => 472.00, 'baseline_is_multiple' => 0],
            ['id' => 34, 'indicator_id' => 34, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 35, 'indicator_id' => 35, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 36, 'indicator_id' => 36, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 37, 'indicator_id' => 37, 'baseline_value' => 10.00, 'baseline_is_multiple' => 0],
            ['id' => 38, 'indicator_id' => 38, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 39, 'indicator_id' => 39, 'baseline_value' => 19.00, 'baseline_is_multiple' => 0],
            ['id' => 40, 'indicator_id' => 40, 'baseline_value' => 5440.00, 'baseline_is_multiple' => 0],
            ['id' => 41, 'indicator_id' => 41, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 42, 'indicator_id' => 42, 'baseline_value' => 4.00, 'baseline_is_multiple' => 0],
            ['id' => 43, 'indicator_id' => 43, 'baseline_value' => 377.00, 'baseline_is_multiple' => 0],
            ['id' => 44, 'indicator_id' => 44, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 45, 'indicator_id' => 45, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 46, 'indicator_id' => 46, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 47, 'indicator_id' => 47, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 48, 'indicator_id' => 48, 'baseline_value' => 7.00, 'baseline_is_multiple' => 0],
            ['id' => 49, 'indicator_id' => 49, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 50, 'indicator_id' => 50, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 51, 'indicator_id' => 51, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
            ['id' => 52, 'indicator_id' => 52, 'baseline_value' => 0.00, 'baseline_is_multiple' => 0],
        ];




        foreach ($data as $baseline) {


            if($baseline['indicator_id'] ==20){
                $baseline['baseline_is_multiple'] = 1;
              $base =   Baseline::create($baseline);





                BaselineDataMultiple::create([
                    'indicator_id' => $baseline['indicator_id'],
                    'baseline_data_id' => $base->id,
                    'baseline_value' => 0,
                    'name' => 'OFSP',
                    'unit_type' => 'Ton(s)',

                ],
            );

            BaselineDataMultiple::create([
                'indicator_id' => $baseline['indicator_id'],
                'baseline_data_id' => $base->id,
                'baseline_value' => 0,
                'name' => 'Potato',
                'unit_type' => 'Bundle(s)',

            ],
        );

        BaselineDataMultiple::create([
            'indicator_id' => $baseline['indicator_id'],
            'baseline_data_id' => $base->id,
            'baseline_value' => 0,
            'name' => 'Cassava',
            'unit_type' => 'Bundle(s)',

        ],
    );
            }else{
                $base =   Baseline::create($baseline);

            }

        }




    }
}
