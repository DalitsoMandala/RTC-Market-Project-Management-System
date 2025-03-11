<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SeedBeneficiariesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $sqlFile = database_path('seed_beneficiaries.sql');

        if (File::exists($sqlFile)) {
            DB::unprepared(File::get($sqlFile)); // Run the SQL file
            $this->command->info('OFSP data seeded successfully!');
        } else {
            $this->command->error('SQL file not found!');
        }
    }
}