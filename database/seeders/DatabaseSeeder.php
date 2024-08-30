<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OrganisationSeeder::class);  //done
        $this->call(UserSeeder::class); // done
        $this->call(SystemSeeder::class); // done
        $this->call(CgiarProjectSeeder::class); //
        $this->call(ReportPeriodSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(IndicatorSeeder::class);
        $this->call(IndicatorTargetSeeder::class);
        $this->call(AssignedTargetSeeder::class);
        $this->call(DisaggregationSeeder::class);
        $this->call(FormSeeder::class);
        $this->call(PeriodSeeder::class);
        $this->call(SubmissionSeeder::class);
        $this->call(SourceSeeder::class);
        $this->call(IndicatorClassSeeder::class);
        Artisan::call('exchange-rates:fetch');
    }
}
