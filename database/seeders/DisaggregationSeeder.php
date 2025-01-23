<?php

namespace Database\Seeders;

use App\Models\Indicator;
use Illuminate\Database\Seeder;
use App\Helpers\IndicatorsContent;
use Illuminate\Support\Facades\Log;
use App\Models\IndicatorDisaggregation;
use App\Helpers\rtc_market\indicatorBuilder;

class DisaggregationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //





        $indicatorArray = new IndicatorsContent();
        $disagg = $indicatorArray->indicatorDisaggregation();

        foreach ($disagg as $indicatorName => $data) {


            $name = $indicatorName;
            $contentArray = new IndicatorsContent(name: $name);
            $content = $contentArray->content();


            if ($content->isNotEmpty()) {
                foreach ($data as $disag) {
                    IndicatorDisaggregation::create([
                        'name'         => $disag,
                        'indicator_id' => $content['id'],
                    ]);
                }
            }
        }
    }
}
