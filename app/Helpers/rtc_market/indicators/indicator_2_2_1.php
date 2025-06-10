<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\Recruitment;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_1
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

        $indicator = Indicator::where('indicator_name', 'Number of private sector actors involved in production of RTC certified seed')->where('indicator_no', '2.2.1')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

        return $this->applyFilters($query, true);
    }


    public function builderFarmer(): Builder
    {
        $query = Recruitment::query()->where('recruitments.status', 'approved');

        return $this->applyFilters($query);
    }

    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()->where('rtc_production_processors.status', 'approved');
        return $this->applyFilters($query);
    }
    public function getTotals()
    {

        $builder = $this->builder()->get();

        $indicator = Indicator::where('indicator_name', 'Number of private sector actors involved in production of RTC certified seed')->first();
        $disaggregations = $indicator->disaggregations;
        $data = collect([]);
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
                    if (!$this->enterprise || !$isEnterpriseKey || str_contains($key, $this->enterprise)) {
                        if ($json->has($key)) {
                            $data->put($key, $data->get($key) + $json[$key]);
                        }
                    }
                }
            });
        });

        return $data;
    }

    public function findCropCount()
    {
        if ($this->enterprise) {
            $farmerTotal = $this->builderFarmer()->where('sector', '=', 'Private')
                ->where('category', '=', 'Seed multiplier')->count();

            return [
                strtolower(str_replace(' ', '_', $this->enterprise)) => $farmerTotal,
            ];
        }
        // Count for Cassava
        $cassavaTotal = $this->builderFarmer()
            ->where('enterprise', '=', 'Cassava')
            ->where('sector', '=', 'Private')
            ->where('category', '=', 'Seed multiplier')
            ->count();

        // Count for Sweet potato
        $sweetPotatoTotal = $this->builderFarmer()
            ->where('enterprise', '=', 'Sweet potato')
            ->where('sector', '=', 'Private')
            ->where('category', '=', 'Seed multiplier')
            ->count();

        // Count for Potato
        $potatoTotal = $this->builderFarmer()
            ->where('enterprise', '=', 'Potato')
            ->where('sector', '=', 'Private')
            ->where('category', '=', 'Seed multiplier')
            ->count();

        return [
            'cassava' => $cassavaTotal,
            'potato' => $potatoTotal,
            'sweet_potato' => $sweetPotatoTotal,
        ];
    }



    public function getDisaggregations()
    {
        $crop = $this->findCropCount();

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
