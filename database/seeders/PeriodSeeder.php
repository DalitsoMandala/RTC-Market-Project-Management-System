<?php
namespace Database\Seeders;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $dateEstablished = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');             // Today's date
        $dateEnding      = Carbon::now()->addMonth()->startOfDay()->format('Y-m-d H:i:s'); // Date one month from today

        foreach (Indicator::with('forms', 'disaggregations')->get() as $index => $indicator) {
            foreach (FinancialYear::all() as $financialYear) {
                if ($indicator->forms->count() > 0) {
                    foreach ($indicator->forms as $form) {
                        SubmissionPeriod::create([
                            'form_id'                   => $form->id,
                            'date_established'          => $dateEstablished,
                            'date_ending'               => $dateEnding,
                            'reporting_period_month_id' => 1,
                            'financial_year_id'         => $financialYear->id,
                            'indicator_id'              => $indicator->id,
                            'is_open'                   => true,
                            'is_expired'                => false,
                        ]);

                    }

                    $indicatorDis = $indicator->disaggregations->first();

                    SubmissionTarget::create([
                        //     'reporting_period_month_id' => 1,
                        'financial_year_id' => $financialYear->id,
                        'indicator_id'      => $indicator->id,
                        'target_name'       => $indicatorDis->name,
                        'target_value'      => rand(50, 100) * 10,
                    ]);
                }
            }

            if ($index === 3) {
                break; // Stop after 4 indicators to save time. You can remove this line if you want to create all indicators.
            }

        }

    }
}
