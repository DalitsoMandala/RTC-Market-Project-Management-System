<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class indicator_B3
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

        $indicator = Indicator::where('indicator_no', 'B3')->first();
        $query     = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
    }

    public function getTotals()
    {
        $data = collect([
            'Total (% Percentage)'   => 0,
            'Volume (Metric Tonnes)' => 0,
            'Financial value ($)'    => 0,
            '(Formal) Cassava'       => 0,
            '(Formal) Potato'        => 0,
            '(Formal) Sweet potato'  => 0,
        ]);

        // Process the builder in chunks
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

        return $data;
    }

    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_no', 'B3')->first();
        return $indicator ?? Log::error('Indicator not found', [
            'indicator_no' => 'B3',
        ]);
    }
    public function getDisaggregations()
    {

        // Get the totals
        $totals = $this->getTotals();

        // Subtotal based on (Formal) Cassava, (Formal) Potato, (Formal) Sweet potato
        $subTotal = $totals['(Formal) Cassava'] + $totals['(Formal) Potato'] + $totals['(Formal) Sweet potato'];

        // Retrieve the indicator
        $indicator = $this->findIndicator();

        // Return the disaggregated data
        return [
            'Total'          => 0,
            'Households'     => 0,
            'School feeding' => 0,
            'Caregroups'     => 0,
        ];
    }
}
