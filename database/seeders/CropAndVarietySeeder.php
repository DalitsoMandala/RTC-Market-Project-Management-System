<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CropAndVarietySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $crops = [
            'Potato',
            'Sweet potato',
            'Cassava'
        ];

        foreach ($crops as $crop) {
            $mainCrop = Crop::create([
                'name' => $crop
            ]);

            switch ($mainCrop->name) {
                case 'Potato':
                    $mainCrop->varieties()->createMany([
                        ['name' => 'violet'],
                        ['name' => 'chuma'],
                        ['name' => 'mwai'],
                        ['name' => 'zikomo'],
                        ['name' => 'thandizo'],
                        ['name' => 'other'],
                    ]);
                    break;

                case 'Sweet potato':
                    $mainCrop->varieties()->createMany([
                        ['name' => 'royal choice'],
                        ['name' => 'kaphulira'],
                        ['name' => 'chipika'],
                        ['name' => 'mathuthu'],
                        ['name' => 'kadyaubwelere'],
                        ['name' => 'sungani'],
                        ['name' => 'kajiyani'],
                        ['name' => 'mugamba'],
                        ['name' => 'kenya'],
                        ['name' => 'zondeni'],
                        ['name' => 'nyamoyo'],
                        ['name' => 'anaakwanire'],
                        ['name' => 'other'],
                    ]);
                    break;
                case 'Cassava':
                    // code...

                    $mainCrop->varieties()->createMany([

                        ['name' => 'other'],
                    ]);
                    break;
                default:
                    // code...
                    break;
            }
        }
    }
}
