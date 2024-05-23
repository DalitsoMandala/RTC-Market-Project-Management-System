<?php

namespace Database\Seeders;

use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();
        Submission::create([
            'user_id' => 4,
            'batch_no' => Str::random(),
            'form_id' => 1,
            'status' => 'approved',
            'period_id' => 1,
            'comments' => $faker->paragraph(3)
        ]);
    }
}