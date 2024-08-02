<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\RpmFarmerFollowUp;
use App\Models\RpmProcessorFollowUp;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Illuminate\Contracts\Database\Eloquent\Builder;

class indicator_B1
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;
    protected $financial_year, $reporting_period, $project;
    public function __construct($reporting_period = null, $financial_year = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;

    }
    public function Farmerbuilder(): Builder
    {

        $query = RtcProductionFarmer::query()->where('name_of_actor', '!=', null);



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

    public function FarmerFollowupbuilder(): Builder
    {

        $query = RpmFarmerFollowUp::query();


        if ($this->reporting_period && $this->financial_year) {
            $hasData = false;
            $data = RtcProductionFarmer::where('period_month_id', $this->reporting_period)->where('financial_year_id', $this->financial_year);
            if ($data->get()->isNotEmpty()) {

                $hasData = true;
                $dataIds = $data->get()->pluck('id');

                $data2 = $query->whereIn('rpm_farmer_id', $dataIds);

                $query = $data2;
            }


            if (!$hasData) {
                // No data found, return an empty collection
                return $query->whereIn('id', []);
            }
        }

        return $query;

    }

    public function Processorbuilder(): Builder
    {

        $query = RtcProductionProcessor::query();

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

    public function ProcessorFollowupbuilder(): Builder
    {

        $query = RpmProcessorFollowUp::query();


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

    public function findCropCount()
    {
        $farmer = $this->Farmerbuilder()->get();
        $followupFarmer = $this->FarmerFollowupbuilder()->get();
        $data = collect([
            'potato' => 0,
            'cassava' => 0,
            'sweet_potato' => 0,
        ]);

        $farmer->each(function ($model) use ($data) {
            $json = collect(json_decode($model->number_of_plantlets_produced, true));

            if ($json->has('potato')) {

                $data->put('potato', $data->get('potato') + $json['potato']);
            }

            if ($json->has('cassava')) {
                $data->put('cassava', $data->get('cassava') + $json['cassava']);
            }

            if ($json->has('sweet_potato')) {
                $data->put('sweet_potato', $data->get('sweet_potato') + $json['sweet_potato']);
            }
        });


        $followupFarmer->each(function ($model) use ($data) {
            $json = collect(json_decode($model->number_of_plantlets_produced, true));

            if ($json->has('potato')) {

                $data->put('potato', $data->get('potato') + $json['potato']);
            }

            if ($json->has('cassava')) {
                $data->put('cassava', $data->get('cassava') + $json['cassava']);
            }

            if ($json->has('sweet_potato')) {
                $data->put('sweet_potato', $data->get('sweet_potato') + $json['sweet_potato']);
            }
        });


        return $data;
    }
    public function findTotal()
    {
        return [
            'farmer' => $this->Farmerbuilder()->count(),
            'farmer_followup' => $this->FarmerFollowupbuilder()->count(),
        ];
    }

    public function getDisaggregations()
    {

        $total = $this->findTotal()['farmer'] + $this->findTotal()['farmer_followup'];
        $crop = $this->findCropCount();
        return [
            'Total' => $total,
            'Cassava' => $crop['cassava'],
            'Sweet potato' => $crop['sweet_potato'],
            'Potato' => $crop['potato'],
        ];

    }

}