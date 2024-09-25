<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


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


        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }


        return $query;

    }

    public function getTotals()
    {

        $builder = $this->builder()->get();
        $data = collect([
            "Raw" => 0,
            "Total" => 0,
            "Potato" => 0,
            "Cassava" => 0,
            "Processed" => 0,
            "Sweet potato" => 0,
            "Formal exports" => 0,
            "Informal exports" => 0,
            "Financial value ($)" => 0,
            "Volume (Metric Tonnes)" => 0,
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
            "Raw" => $totals['Raw'],
            "Total" => $totals['Total'],
            "Potato" => $totals['Potato'],
            "Cassava" => $totals['Cassava'],
            "Processed" => $totals['Processed'],
            "Sweet potato" => $totals['Sweet potato'],
            "Formal exports" => $totals['Formal exports'],
            "Informal exports" => $totals['Informal exports'],
            "Financial value ($)" => $totals['Financial value ($)'],
            "Volume (Metric Tonnes)" => $totals['Volume (Metric Tonnes)'],
        ];

    }



}
