<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_3
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

        $indicator = Indicator::where('indicator_name', 'Percentage seed multipliers with formal registration')->where('indicator_no', '2.2.3')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id);

        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = $query->where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                return $data;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }
        if ($this->organisation_id && $this->target_year_id) {
            $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
            $query = $data;

        } else
            if ($this->organisation_id && $this->target_year_id == null) {
                $data = $query->where('organisation_id', $this->organisation_id);
                $query = $data;

            }




        return $query;

    }

    public function getTotals()
    {

        $builder = $this->builder()->get();


        $indicator = Indicator::where('indicator_name', 'Percentage seed multipliers with formal registration')->where('indicator_no', '2.2.3')->first();
        $disaggregations = $indicator->disaggregations;
        $data = collect([]);
        $disaggregations->pluck('name')->map(function ($item) use (&$data) {
            $data->put($item, 0);
        });




        if ($builder->isNotEmpty()) {


            $builder->each(function ($model) use ($data) {
                $json = collect(json_decode($model->data, true));



                foreach ($data as $key => $dt) {

                    if ($json->has($key)) {

                        $data->put($key, $data->get($key) + $json[$key]);
                    }
                }

            });


        }

        return $data;
    }
    public function getDisaggregations()
    {

        return $this->getTotals()->toArray();
    }

}