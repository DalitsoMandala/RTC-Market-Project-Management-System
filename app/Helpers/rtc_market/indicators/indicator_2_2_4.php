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


        $query = SeedBeneficiary::query()->where('status', 'approved');






        return $this->applySeedFilters($query);
    }


    public function getDisaggregations()
    {
        $query = $this->builder();

        // Initialize all crops with 0 values
        $crops = [
            'Cassava' => 0,
            'Potato' => 0,
            'Sweet potato' => 0,
        ];

        // If enterprise is set at constructor level, we only want that one crop
        if ($this->enterprise) {

            $bundles = $query->sum('bundles_received');
            $crops[$this->enterprise] = (int)$bundles;
        }
        // Otherwise get all crops (enterprise filter not set)  
        else {
            // Need to temporarily disable enterprise filter for these queries
            $tempEnterprise = $this->enterprise;
            $this->enterprise = null;

            $crops['Cassava'] = (int)$this->builder()->where('crop', 'Cassava')->sum('bundles_received');
            $crops['Potato'] = (int)$this->builder()->where('crop', 'Potato')->sum('bundles_received');
            $crops['Sweet potato'] = (int)$this->builder()->where('crop', 'OFSP')->sum('bundles_received');

            $this->enterprise = $tempEnterprise;
        }

        return $crops;
    }
}