<?php



namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class Indicator_B2
{
    protected $financial_year, $reporting_period, $project;
    public function __construct($reporting_period = null, $financial_year = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;

    }

    public function builder(): Builder
    {

        $indicator = Indicator::where('indicator_name', 'Percentage increase in value of formal RTC exports')->where('indicator_no', 'B2')->first();

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





        return $query;

    }

    public function getRawProcessedData()
    {

        $builder = $this->builder()->get();

        if ($builder->isNotEmpty()) {


        }
    }


    public function getDisaggregations()
    {

        $this->getRawProcessedData();

        return [
            "Raw" => 0,
            "Total" => 0,
            "Potato" => 0,
            "Cassava" => 0,
            "Processed" => 0,
            "Sweet potato" => 0,
            "Formal exports" => 0,
            "Informal exports" => 0,
            "Financial value ($)" => 0,
            "Volume (Metric Tonnes)" => 0,
        ];

    }



}