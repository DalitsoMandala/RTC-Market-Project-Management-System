<?php

namespace App\Helpers\rtc_market\indicators;

use Log;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log as Logger;


class indicator_B2
{
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $target_year_id;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;
    }
    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Percentage increase in value of formal RTC exports')->where('indicator_no', 'B2')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        // Check if both reporting period and financial year are set
        if ($this->reporting_period || $this->financial_year) {
            // Apply filter for reporting period if it's set
            if ($this->reporting_period) {
                $query->where('period_month_id', $this->reporting_period);
            }

            // Apply filter for financial year if it's set
            if ($this->financial_year) {
                $query->where('financial_year_id', $this->financial_year);
            }

            // If no data is found, return an empty result
            if (!$query->exists()) {
                $query->whereIn('id', []); // Empty result filter
            }
        }

        // Filter by organization if set
        if ($this->organisation_id) {
            $query->where('organisation_id', $this->organisation_id);
        }




        return $query;
    }

    public function getTotals()
    {

        $builder = $this->builder()->get();
        $data = collect([
            'Total (% Percentage)' => 0,
            'Volume (Metric Tonnes)' => 0,
            'Financial value ($)' => 0,
            '(Formal) Cassava' => 0,
            '(Formal) Potato' => 0,
            '(Formal) Sweet potato' => 0,
            '(Informal) Cassava' => 0,
            '(Informal) Potato' => 0,
            '(Informal) Sweet potato' => 0,
            'Raw' => 0,
            'Processed' => 0,

        ]);

        if ($builder->isNotEmpty()) {
            // Process the builder in chunks of 100 (you can adjust this number as needed)
            $this->builder()->chunk(100, function ($models) use (&$data) {
                $models->each(function ($model) use (&$data) {
                    $json = collect(json_decode($model->data, true));

                    foreach ($data as $key => $dt) {
                        if ($json->has($key)) {
                            $data->put($key, $data->get($key) + $json[$key]);
                        }
                    }
                });
            });
        }

        return $data;
    }

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage increase in value of formal RTC exports')->where('indicator_no', 'B2')->first();
        return $indicator ?? Logger::error('Indicator not found');
    }
    public function getDisaggregations()
    {

        $totals = $this->getTotals();
        $subTotal = $totals['(Formal) Cassava'] + $totals['(Formal) Potato'] + $totals['(Formal) Sweet potato'] + $totals['(Informal) Cassava'] + $totals['(Informal) Potato'] + $totals['(Informal) Sweet potato'];
        $indicator = $this->findIndicator();
        $baseline = $indicator->baseline->baseline_value ?? 0;
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);
        $finalTotalPercentage = $percentageIncrease->percentage();
        return [
            "Total (% Percentage)" => $finalTotalPercentage,
            '(Formal) Cassava' => $totals['(Formal) Cassava'],
            '(Formal) Potato' => $totals['(Formal) Potato'],
            '(Formal) Sweet potato' => $totals['(Formal) Sweet potato'],
            '(Informal) Cassava' => $totals['(Informal) Cassava'],
            '(Informal) Potato' => $totals['(Informal) Potato'],
            '(Informal) Sweet potato' => $totals['(Informal) Sweet potato'],
            'Raw' => $totals['Raw'],
            'Processed' => $totals['Processed'],
            "Financial value ($)" => $subTotal,
            //"Volume (Metric Tonnes)" => $totals['Volume (Metric Tonnes)'],
        ];
    }
}
