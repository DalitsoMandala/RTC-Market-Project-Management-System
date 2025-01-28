<?php

namespace Database\Seeders;

use App\Models\ReportingPeriod;
use App\Models\ReportingPeriodMonth;
use Illuminate\Database\Seeder;

class ReportPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $reportingPeriods = [
            'QUARTERLY',
            'MONTHLY',
            'ANNUALLY'
        ];

        foreach ($reportingPeriods as $period) {
            $reportingPeriod = ReportingPeriod::create([
                'name' => $period,

            ]);

            if ($period === 'QUARTERLY') {

                ReportingPeriodMonth::create([
                    'period_id' => $reportingPeriod->id,
                    'start_month' => 'JANUARY',
                    'end_month' => 'MARCH',
                    'type' => 'QUARTER 1'
                ]);

                ReportingPeriodMonth::create([
                    'period_id' => $reportingPeriod->id,
                    'start_month' => 'APRIL',
                    'end_month' => 'JUNE',
                    'type' => 'QUARTER 2'
                ]);

                ReportingPeriodMonth::create([
                    'period_id' => $reportingPeriod->id,
                    'start_month' => 'JULY',
                    'end_month' => 'SEPTEMBER',
                    'type' => 'QUARTER 3'
                ]);

                ReportingPeriodMonth::create([
                    'period_id' => $reportingPeriod->id,
                    'start_month' => 'OCTOBER',
                    'end_month' => 'DECEMBER',
                    'type' => 'QUARTER 4'
                ]);

                ReportingPeriodMonth::create([
                    'period_id' => $reportingPeriod->id,
                    'start_month' => 'UNSPECIFIED',
                    'end_month' => 'UNSPECIFIED',
                    'type' => 'UNSPECIFIED'
                ]);
            }
        }
    }
}
