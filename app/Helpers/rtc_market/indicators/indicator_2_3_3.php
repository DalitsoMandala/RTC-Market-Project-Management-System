<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use App\Models\Indicator;
use App\Models\SubmissionReport;
use App\Helpers\IncreasePercentage;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_3_3
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

        $indicator = Indicator::where('indicator_name', 'Number of registered seed producers accessing markets through online Market Information System (MIS)')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');



        return $this->applyFilters($query);
    }

    public function getTotals()
    {

        $builder = $this->builder()->get();

        $indicator = Indicator::where('indicator_name', 'Number of registered seed producers accessing markets through online Market Information System (MIS)')->first();
        $disaggregations = $indicator->disaggregations;
        $data = collect([]);
        $disaggregations->pluck('name')->map(function ($item) use (&$data) {
            $data->put($item, 0);
        });




        $this->builder()->chunk(100, function ($models) use (&$data) {
            $models->each(function ($model) use (&$data) {
                // Decode the JSON data from the model
                $json = collect(json_decode($model->data, true));

                // Add the values for each key to the totals
                foreach ($data as $key => $dt) {
                    if ($json->has($key)) {
                        $data->put($key, $data->get($key) + $json[$key]);
                    }
                }
            });
        });

        return $data;
    }

    public function getDisaggregations()
    {

        $total = $this->getTotals()['Domestic markets'] + $this->getTotals()['International markets'];
        $cassava = $this->getTotals()['Cassava'];
        $potato = $this->getTotals()['Potato'];
        $sweet_potato = $this->getTotals()['Sweet potato'];
        $individual_farmers = $this->getTotals()['Individual farmers not in POs'];
        $pos = $this->getTotals()['POs'];
        return [
            'Total' => $total,
            'Cassava' => $cassava,
            'Potato' => $potato,
            'Sweet potato' => $sweet_potato,
            'Domestic markets' => $this->getTotals()['Domestic markets'],
            'International markets' => $this->getTotals()['International markets'],
            'Individual farmers not in POs' => $individual_farmers,
            'POs' => $pos,
            'Large scale commercial farmers' => $this->getTotals()['Large scale commercial farmers'],


        ];
    }
}
