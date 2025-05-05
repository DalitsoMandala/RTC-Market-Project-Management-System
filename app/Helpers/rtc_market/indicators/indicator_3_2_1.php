<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\Recruitment;
use App\Models\RtcProductionFarmer;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_2_1
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

        $query = RtcProductionFarmer::query()->where('uses_certified_seed', true)->where('status', 'approved');





        return $this->applyFilters($query);
    }

    public function builderRecruitment(): Builder
    {

        $query = Recruitment::query()->where('uses_certified_seed', true)->where('status', 'approved');





        return $this->applyFilters($query);
    }

    public function getCropTotal()
    {

        $totalPotato = $this->builder()->where('enterprise', 'Potato')->count();
        $totalCassava = $this->builder()->where('enterprise', 'Cassava')->count();
        $totalSweetPotato = $this->builder()->where('enterprise', 'Sweet potato')->count();
        $totalPotatoRecruitment = $this->builderRecruitment()->where('enterprise', 'Potato')->count();
        $totalCassavaRecruitment = $this->builderRecruitment()->where('enterprise', 'Cassava')->count();
        $totalSweetPotatoRecruitment = $this->builderRecruitment()->where('enterprise', 'Sweet potato')->count();


        return [
            'Potato' => $totalPotato + $totalPotatoRecruitment,
            'Cassava' => $totalCassava + $totalCassavaRecruitment,
            'Sweet potato' => $totalSweetPotato + $totalSweetPotatoRecruitment,
        ];
    }



    public function getDisaggregations()
    {
        $this->getCropTotal();
        return [
            'Total' => $this->getCropTotal()['Cassava'] + $this->getCropTotal()['Potato'] + $this->getCropTotal()['Sweet potato'],
            'Cassava' => $this->getCropTotal()['Cassava'],
            'Potato' => $this->getCropTotal()['Potato'],
            'Sweet potato' => $this->getCropTotal()['Sweet potato']
        ];
    }
}