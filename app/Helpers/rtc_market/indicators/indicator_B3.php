<?php



namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class Indicator_B3
{
    protected $financial_year, $reporting_period, $project;
    public function __construct($reporting_period = null, $financial_year = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;

    }
    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Percentage of value ($) of formal RTC imports substituted through local production')->where('indicator_no', 'B3')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id);

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }





        return $query;

    }

    public function getTotals()
    {

        $builder = $this->builder()->get();
        $data = collect([
            "Total" => 0,
            "Volume(Metric Tonnes)" => 0,
            "Financial value ($)" => 0,
            "Cassava" => 0,
            "Potato" => 0,
            "Sweet potato" => 0,
            "Formal" => 0,
        ]);

        if ($builder->isNotEmpty()) {


            $builder->each(function ($model) use ($data) {
                $json = collect(json_decode($model->data, true));



                foreach ($data as $key => $dt) {

                    if ($json->has($key)) {

                        $data->put($key, $data->get($key) + $json[$key]);
                    }
                }

            });


        }

        return $data;
    }


    public function getDisaggregations()
    {

        $totals = $this->getTotals();

        return [
            "Total" => $totals['Total'],
            "Volume(Metric Tonnes)" => $totals['Volume(Metric Tonnes)'],
            "Financial value ($)" => $totals['Financial value ($)'],
            "Cassava" => $totals['Cassava'],
            "Potato" => $totals['Potato'],
            "Sweet potato" => $totals['Sweet potato'],
            "Formal" => $totals['Formal'],

        ];

    }

}
