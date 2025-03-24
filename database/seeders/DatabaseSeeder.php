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
        $this->call(DisaggregationSeeder::class);
        $this->call(FormSeeder::class);
        //     $this->call(PeriodSeeder::class); //edit this (test)
        //  $this->call(SourceSeeder::class);
        $this->call(IndicatorClassSeeder::class);
        $this->call(BaselineSeeder::class);

        //    $this->call(DataGenerationSeeder::class); //edit this (test)
        $this->call(SubmissionTargetSeeder::class);
        Artisan::call('update:information');
    }
}
