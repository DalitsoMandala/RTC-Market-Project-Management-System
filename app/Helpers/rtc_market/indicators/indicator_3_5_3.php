<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_5_3
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;



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
        $query = HouseholdRtcConsumption::query()->where('status', 'approved');





        return $this->applyFilters($query);
    }
    public function getDisaggregations()
    {
        $total = $this->builder()->count();
        return [
            'Total (% Percentage)' => 0
        ];
    }
}
