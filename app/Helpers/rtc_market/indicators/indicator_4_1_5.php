<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;

class indicator_4_1_5
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

        $indicator = Indicator::where('indicator_no', '4.1.5')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
    }

    public function getTotals()
    {

        $builder = $this->builder()->get();

        $indicator       = Indicator::where('indicator_no', '4.1.2')->first();
        $disaggregations = $indicator->disaggregations;
        $data            = collect([
            'Total'             => 0,
            'PO\'s'             => 0,
            'Large scale farms' => 0,
            'SMEs'              => 0,
            'Cassava'           => 0,
            'Potato'            => 0,
            'Sweet potato'      => 0,
        ]);
        $disaggregations->pluck('name')->map(function ($item) use (&$data) {
            $data->put($item, 0);
        });

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
                    if (! $this->enterprise || ! $isEnterpriseKey || str_contains($key, $this->enterprise)) {
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

        // $cassava = $this->builder()->where('enterprise', 'Cassava')->count();
        // $sweet_potato = $this->builder()->where('enterprise', 'Sweet potato')->count();
        // $potato = $this->builder()->where('enterprise', 'Potato')->count();
        // $Smes = $this->builder()->where('type', 'Small medium enterprise (SME)')->count();
        // $large_scale_commercial_farms = $this->builder()->where('type', 'Large scale Processor')->count();
        // $po = $this->builder()->where('type', 'Producer organization (PO)')->count();

        $cassava      = $this->getTotals()['Cassava'] ?? 0;
        $sweet_potato = $this->getTotals()['Sweet potato'] ?? 0;
        $potato       = $this->getTotals()['Potato'] ?? 0;

        return [
            'Total'             => 0,
            'PO\'s'             => 0,
            'Large scale farms' => 0,
            'SMEs'              => 0,
            'Cassava'           => 0,
            'Potato'            => 0,
            'Sweet potato'      => 0,
        ];
    }
}
