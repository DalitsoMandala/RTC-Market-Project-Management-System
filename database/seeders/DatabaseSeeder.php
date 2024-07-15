<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OrganisationSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SystemSeeder::class);
        $this->call(CgiarProjectSeeder::class);
        $this->call(ReportPeriodSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(PartnerSeeder::class);
        $this->call(IndicatorSeeder::class);
        $this->call(IndicatorTargetSeeder::class);
        $this->call(DisaggregationSeeder::class);
        $this->call(FormSeeder::class);
        $this->call(PeriodSeeder::class);
        $this->call(SubmissionSeeder::class);
        $this->call(SourceSeeder::class);
    }
}