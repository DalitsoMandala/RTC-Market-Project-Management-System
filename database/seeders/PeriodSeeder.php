<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\Indicator;
use Illuminate\Database\Seeder;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Illuminate\Support\Facades\DB;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $dateEstablished = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');  // Today's date
        $dateEnding = Carbon::now()->addMonth()->startOfDay()->format('Y-m-d H:i:s');  // Date one month from today





        foreach (Indicator::with('forms', 'disaggregations')->get() as $indicator) {


            if ($indicator->forms->count() > 0) {
                foreach ($indicator->forms as $form) {
                    SubmissionPeriod::create([
                        'form_id' => $form->id,
                        'date_established' => $dateEstablished,
                        'date_ending' => $dateEnding,
                        'month_range_period_id' => 1,
                        'financial_year_id' => 1,
                        'indicator_id' => $indicator->id,
                        'is_open' => true,
                        'is_expired' => false,
                    ]);


                }

                $indicatorDis = $indicator->disaggregations->first();



                SubmissionTarget::create([
                    'month_range_period_id' => 1,
                    'financial_year_id' => 1,
                    'indicator_id' => $indicator->id,
                    'target_name' => $indicatorDis->name,
                    'target_value' => 100,
                ]);
            }

        }

    }
}
