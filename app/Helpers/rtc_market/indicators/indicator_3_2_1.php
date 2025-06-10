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
        if ($this->enterprise) {

            $Total = $this->builder()->count() + $this->builderRecruitment()->count();

            return [
                strtolower(str_replace(' ', '_', $this->enterprise)) => $Total,
            ];
        }

        $totalPotato = $this->builder()->where('enterprise', 'Potato')->count();
        $totalCassava = $this->builder()->where('enterprise', 'Cassava')->count();
        $totalSweetPotato = $this->builder()->where('enterprise', 'Sweet potato')->count();
        $totalPotatoRecruitment = $this->builderRecruitment()->where('enterprise', 'Potato')->count();
        $totalCassavaRecruitment = $this->builderRecruitment()->where('enterprise', 'Cassava')->count();
        $totalSweetPotatoRecruitment = $this->builderRecruitment()->where('enterprise', 'Sweet potato')->count();


        return [
            'potato' => $totalPotato + $totalPotatoRecruitment,
            'cassava' => $totalCassava + $totalCassavaRecruitment,
            'sweet_potato' => $totalSweetPotato + $totalSweetPotatoRecruitment,
        ];
    }



    public function getDisaggregations()
    {
        $crop = $this->getCropTotal();

        // Define all possible crops with default 0 values
        $allCrops = [
            'Cassava' => 0,
            'Sweet potato' => 0,
            'Potato' => 0,
        ];

        // Merge actual values (if they exist)
        foreach ($allCrops as $key => $value) {
            $snakeKey = strtolower(str_replace(' ', '_', $key));
            if (isset($crop[$snakeKey])) {
                $allCrops[$key] = round($crop[$snakeKey], 2);
            }
        }


        $total = array_sum($allCrops);
        return [
            'Total' => $total,
            ...$allCrops,
        ];
    }
}