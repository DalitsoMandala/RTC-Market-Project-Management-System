<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SeedBeneficiary;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_2_2_4
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


        $query = SeedBeneficiary::query()->where('status', 'approved');






        return $this->applyFilters($query);
    }


    public function getDisaggregations()
    {

        $cassava = $this->builder()->where('crop', 'Cassava')->sum('bundles_received');
        $potato = $this->builder()->where('crop', 'Potato')->sum('bundles_received');
        $sweetPotato = $this->builder()->where('crop', 'OFSP')->sum('bundles_received');
        return [
            'Total' => $potato + $sweetPotato + $cassava,
            'Cassava' => (int) $cassava,
            'Potato' => (int) $potato,
            'Sweet potato' => (int) $sweetPotato
        ];
    }
}
