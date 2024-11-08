<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use Illuminate\Database\Eloquent\Builder;


class indicator_B6
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

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage increase in RTC investment')->where('indicator_no', 'B6')->first();
        if (!$indicator) {
            Log::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }

    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Percentage increase in RTC investment')->where('indicator_no', 'B6')->first();

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
        // Initialize the totals for the relevant fields
        $data = collect([
            'Total (% Percentage)' => 0,
            'Cassava' => 0,
            'Potato' => 0,
            'Sweet potato' => 0,
        ]);

        // Process the builder in chunks to prevent memory overload
        $this->builder()->chunk(100, function ($models) use (&$data) {
            $models->each(function ($model) use (&$data) {
                // Decode the JSON data from the model
                $json = collect(json_decode($model->data, true));

                // Add the values for each key to the totals
                foreach ($data as $key => $dt) {
                    if ($json->has($key)) {
                        $data->put($key, $data->get($key) + $json[$key]);
                    }
                }
            });
        });

        return $data;
    }

    public function getDisaggregations()
    {
        // Get the totals from getTotals() method
        $totals = $this->getTotals();

        // Subtotal based on Cassava, Potato, and Sweet potato
        $subTotal = $totals['Cassava'] + $totals['Potato'] + $totals['Sweet potato'];

        // Retrieve the indicator to get the baseline
        $indicator = $this->findIndicator();

        // Get the baseline value, defaulting to 0 if the indicator or baseline doesn't exist
        $baseline = $indicator->baseline->baseline_value ?? 0;

        // Calculate the percentage increase based on the subtotal and baseline
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);
        $finalTotalPercentage = $percentageIncrease->percentage();

        // Return the disaggregated data
        return [
            "Total (% Percentage)" => 0, // Calculated percentage increase
            'Cassava' => $totals['Cassava'],
            'Potato' => $totals['Potato'],
            'Sweet potato' => $totals['Sweet potato'],
        ];
    }
}
