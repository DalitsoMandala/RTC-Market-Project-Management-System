<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Traits\FilterableQuery;
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
        $this->financial_year   = $financial_year;
        $this->organisation_id  = $organisation_id;
        $this->enterprise       = $enterprise;
    }
    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_no', 'B2')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
    }

    public function getTotals()
    {
        $builder = $this->builder()->get();

        // Initialize all possible keys with 0
        $data = collect([
            'Total (% Percentage)'    => 0,
            'Volume (Metric Tonnes)'  => 0,
            'Financial value ($)'     => 0,
            '(Formal) Cassava'        => 0,
            '(Formal) Potato'         => 0,
            '(Formal) Sweet potato'   => 0,
            '(Informal) Cassava'      => 0,
            '(Informal) Potato'       => 0,
            '(Informal) Sweet potato' => 0,
            'Raw'                     => 0,
            'Processed'               => 0,
            'Value of exports'        => 0,
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
                        if (! $this->enterprise || ! $isEnterpriseKey || str_contains($key, $this->enterprise)) {
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
        $indicator = Indicator::where('indicator_no', 'B2')->first();
        return $indicator ?? Logger::error('Indicator not found', [
            'indicator_no' => 'B2',
        ]);
    }
    public function getDisaggregations()
    {

        $totals    = $this->getTotals();
        $subTotal  = $totals['(Formal) Cassava'] + $totals['(Formal) Potato'] + $totals['(Formal) Sweet potato'];
        $indicator = $this->findIndicator();

        return [
            'Total'               => 0,
            'Income ($)'          => 0,
            'Farmers'             => 0,
            'Processors'          => 0,
            'Traders'             => 0,
            'Rolled Baseline'     => 0,
            'Baseline Farmers'    => 0,
            'Baseline Processors' => 0,
            'Baseline Traders'    => 0,
            'Cassava'             => 0,
            'Potato'              => 0,
            'Sweet potato'        => 0,
        ];
    }
}