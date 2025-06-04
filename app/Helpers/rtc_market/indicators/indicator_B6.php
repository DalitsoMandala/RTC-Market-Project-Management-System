<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;

use App\Traits\FilterableQuery;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_B6
{

    use FilterableQuery;
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;


    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage increase in RTC investment')->first();
        if (!$indicator) {
            Log::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }

    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Percentage increase in RTC investment')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
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
        $this->builder()->chunk(1000, function ($models) use (&$data) {
            $models->each(function ($model) use (&$data) {
                // Decode the JSON data from the model
                $json = collect(json_decode($model->data, true));

                // Add the values for each key to the totals
                foreach ($data as $key => $dt) {
                    // Always process non-enterprise keys
                    $isEnterpriseKey = str_contains($key, 'Cassava') ||
                        str_contains($key, 'Potato') ||
                        str_contains($key, 'Sweet potato');

                    // If enterprise is set, only process matching keys or non-enterprise keys
                    if (!$this->enterprise || !$isEnterpriseKey || str_contains($key, $this->enterprise)) {
                        if ($json->has($key)) {
                            $data->put($key, $data->get($key) + $json[$key]);
                        }
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