<?php



namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_B3
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

        $indicator = Indicator::where('indicator_name', 'Percentage of value ($) of formal RTC imports substituted through local production')->where('indicator_no', 'B3')->first();

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
        $data = collect([
            'Total (% Percentage)' => 0,
            'Volume (Metric Tonnes)' => 0,
            'Financial value ($)' => 0,
            '(Formal) Cassava' => 0,
            '(Formal) Potato' => 0,
            '(Formal) Sweet potato' => 0,
        ]);



        // Process the builder in chunks
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

        return $data;
    }

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage of value ($) of formal RTC imports substituted through local production')->where('indicator_no', 'B3')->first();
        return $indicator ?? \Log::error('Indicator not found');
    }
    public function getDisaggregations()
    {


        // Get the totals
        $totals = $this->getTotals();

        // Subtotal based on (Formal) Cassava, (Formal) Potato, (Formal) Sweet potato
        $subTotal = $totals['(Formal) Cassava'] + $totals['(Formal) Potato'] + $totals['(Formal) Sweet potato'];

        // Retrieve the indicator
        $indicator = $this->findIndicator();

        // Get the baseline value, defaulting to 0 if the indicator or baseline doesn't exist
        $baseline = $indicator->baseline->baseline_value ?? 0;

        // Calculate the percentage increase based on the subtotal and baseline
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);
        $finalTotalPercentage = $percentageIncrease->percentage();

        // Return the disaggregated data
        return [
            "Total (% Percentage)" => $finalTotalPercentage, // Calculated percentage increase
            '(Formal) Cassava' => $totals['(Formal) Cassava'],
            '(Formal) Potato' => $totals['(Formal) Potato'],
            '(Formal) Sweet potato' => $totals['(Formal) Sweet potato'],
            // 'Volume (Metric Tonnes)' => $totals['Volume (Metric Tonnes)'], // Volume
            "Financial value ($)" => $subTotal, // Financial value is the subtotal of formal crops
        ];
    }
}
