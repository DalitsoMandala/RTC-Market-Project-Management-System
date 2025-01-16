<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SeedBeneficiary;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

class indicator_2_2_4
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

        //   $indicator = Indicator::where('indicator_name', 'Volume of seed distributed within communities to enhance POs productivity')->where('indicator_no', '2.2.4')->first();

        $query = SeedBeneficiary::query()->where('status', 'approved');

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
        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }




        return $query;
    }

    public function getTotals()
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
    public function getDisaggregations()
    {

        return $this->getTotals();
    }
}
