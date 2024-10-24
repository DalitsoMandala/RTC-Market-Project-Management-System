<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_4_5
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

        $query = RtcProductionFarmer::query()->where('status', 'approved')
            ->where('approach', 'Collective marketing only')
            ->where('type', 'Producer organization (PO)');



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




        return $query;
    }
    public function getCropTotal()
    {

        $totalPotato = $this->builder()->where('enterprise', 'Potato')->count();
        $totalCassava = $this->builder()->where('enterprise', 'Cassava')->count();
        $totalSweetPotato = $this->builder()->where('enterprise', 'Sweet potato')->count();


        return [
            'Potato' => $totalPotato,
            'Cassava' => $totalCassava,
            'Sweet potato' => $totalSweetPotato,
        ];
    }
    public function getMarketSegment()
    {
        return $this->builder()->selectRaw('SUM(IF(market_segment_fresh = 1, 1, 0)) as Fresh, SUM(IF(market_segment_processed = 1, 1, 0)) as Processed')->first()->toArray();
    }
    public function getDisaggregations()
    {

        return [
            'Total' => $this->builder()->count(),
            'Cassava' => $this->getCropTotal()['Cassava'],
            'Potato' => $this->getCropTotal()['Potato'],
            'Sweet potato' => $this->getCropTotal()['Sweet potato'],
            'Fresh' => $this->getMarketSegment()['Fresh'] ?? 0,
            'Processed' => $this->getMarketSegment()['Processed'] ?? 0,
        ];
    }
}
