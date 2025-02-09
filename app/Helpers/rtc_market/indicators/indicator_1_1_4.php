<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;

use App\Traits\FilterableQuery;
use App\Models\SubmissionReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_1_1_4
{
    use FilterableQuery;
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

        $indicator = Indicator::where('indicator_name', 'Percentage increase in adoption of new RTC technologies')->where('indicator_no', '1.1.4')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query);
    }
    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage increase in adoption of new RTC technologies')->where('indicator_no', '1.1.4')->first();
        if (!$indicator) {

            Log::error('Indicator not found');
            return null; // Or throw an exception if needed
        }

        return $indicator;
    }
    public function getTotals()
    {
        // Initialize the totals for the relevant fields
        $data = collect([
            'Total (% Percentage)' => 0,
            'Improved RTC variety' => 0,
            'Seed production' => 0,
            'Storage' => 0,
            'Agronomic production' => 0,
            'Post-harvest processing' => 0,
            'Cassava' => 0,
            'Potato' => 0,
            'Sweet potato' => 0
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
        $totals = $this->getTotals()->toArray();



        $totals['Total (% Percentage)'] = 0;
        return [
            'Total (% Percentage)' => $totals['Total (% Percentage)'],
            'Improved RTC variety' => $totals['Improved RTC variety'],
            'Seed production' => $totals['Seed production'],
            'Storage' => $totals['Storage'],
            'Agronomic production' => $totals['Agronomic production'],
            'Post-harvest processing' => $totals['Post-harvest processing'],
            'Cassava' => $totals['Cassava'],
            'Potato' => $totals['Potato'],
            'Sweet potato' => $totals['Sweet potato']
        ];
    }
}
