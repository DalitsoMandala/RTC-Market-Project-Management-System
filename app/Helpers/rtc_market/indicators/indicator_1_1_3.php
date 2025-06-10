<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class indicator_1_1_3
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
    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Number of new RTC technologies developed')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
    }

    public function getTotals()
    {
        // Initialize the totals for the relevant fields
        $data = collect([
            'Total' => 0,
            'Cassava' => 0,
            'Potato' => 0,
            'Sweet potato' => 0,
            'Fresh' => 0,
            'Processed' => 0,
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
        $totals = $this->getTotals()->toArray();


        // Subtotal based on Cassava, Potato, and Sweet potato
        $subTotal = $totals['Cassava'] + $totals['Potato'] + $totals['Sweet potato'];
        return [
            'Total' => $subTotal,
            'Cassava' => $totals['Cassava'],
            'Potato' => $totals['Potato'],
            'Sweet potato' => $totals['Sweet potato'],
            'Fresh' => $totals['Fresh'],
            'Processed' => $totals['Processed'],
        ];
    }
}