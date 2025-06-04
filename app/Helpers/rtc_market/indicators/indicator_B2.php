<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use Illuminate\Support\Facades\Log;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log as Logger;


class indicator_B2
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

        $indicator = Indicator::where('indicator_name', 'Percentage increase in value of formal RTC exports')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
    }

    public function getTotals()
    {
        $builder = $this->builder()->get();

        // Initialize all possible keys with 0
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
            $this->builder()->chunk(1000, function ($models) use (&$data) {
                $models->each(function ($model) use (&$data) {
                    $json = collect(json_decode($model->data, true));

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
        }

        return $data;
    }

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Percentage increase in value of formal RTC exports')->first();
        return $indicator ?? Logger::error('Indicator not found');
    }
    public function getDisaggregations()
    {

        $totals = $this->getTotals();
        $subTotal = $totals['(Formal) Cassava'] + $totals['(Formal) Potato'] + $totals['(Formal) Sweet potato'];
        $indicator = $this->findIndicator();


        return [
            "Total (% Percentage)" => 0,
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