<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\AttendanceRegister;
use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;


class indicator_3_2_2
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
    // public function builder(): Builder
    // {

    //     $indicator = Indicator::where('indicator_name', 'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)')->where('indicator_no', '3.2.2')->first();

    //     $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

    //     // Check if both reporting period and financial year are set
    //     if ($this->reporting_period || $this->financial_year) {
    //         // Apply filter for reporting period if it's set
    //         if ($this->reporting_period) {
    //             $query->where('period_month_id', $this->reporting_period);
    //         }

    //         // Apply filter for financial year if it's set
    //         if ($this->financial_year) {
    //             $query->where('financial_year_id', $this->financial_year);
    //         }

    //         // If no data is found, return an empty result
    //         if (!$query->exists()) {
    //             $query->whereIn('id', []); // Empty result filter
    //         }
    //     }

    //     // Filter by organization if set
    //     if ($this->organisation_id) {
    //         $query->where('organisation_id', $this->organisation_id);
    //     }




    //     return $query;
    // }

    public function builder(): Builder
    {

        $query = AttendanceRegister::query()->where('status', 'approved');

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

    public function getCropType()
    {

        $cassava = $this->builder()->where('rtcCrop_cassava', true)->count();
        $potato = $this->builder()->where('rtcCrop_potato', true)->count();
        $sweetPotato = $this->builder()->where('rtcCrop_sweet_potato', true)->count();
        return [
            'Cassava' => $cassava,
            'Potato' => $potato,
            'Sweet potato' => $sweetPotato
        ];
    }

    public function getCategory()
    {
        $farmers = $this->builder()->where('designation', 'Farmer')->count();
        $processors = $this->builder()->where('designation', 'Processor')->count();
        $traders = $this->builder()->where('designation', 'Trader')->count();
        $partners = $this->builder()->where('designation', 'Partner')->count();
        $staff = $this->builder()->where('designation', 'Staff')->count();

        return [
            'Farmers' => $farmers,
            'Processors' => $processors,
            'Traders' => $traders,
            'Partners' => $partners,
            'Staff' => $staff
        ];

    }

    // public function getTotals()
    // {

    //     $builder = $this->builder()->get();

    //     $indicator = Indicator::where('indicator_name', 'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)')->where('indicator_no', '3.2.2')->first();
    //     $disaggregations = $indicator->disaggregations;
    //     $data = collect([]);
    //     $disaggregations->pluck('name')->map(function ($item) use (&$data) {
    //         $data->put($item, 0);
    //     });




    //     $this->builder()->chunk(100, function ($models) use (&$data) {
    //         $models->each(function ($model) use (&$data) {
    //             // Decode the JSON data from the model
    //             $json = collect(json_decode($model->data, true));

    //             // Add the values for each key to the totals
    //             foreach ($data as $key => $dt) {
    //                 if ($json->has($key)) {
    //                     $data->put($key, $data->get($key) + $json[$key]);
    //                 }
    //             }
    //         });
    //     });

    //     return $data;
    // }
    public function getDisaggregations(
    ) {
        $cassava = $this->getCropType()['Cassava'];
        $potato = $this->getCropType()['Potato'];
        $sweetPotato = $this->getCropType()['Sweet potato'];
        $farmers = $this->getCategory()['Farmers'];
        $processors = $this->getCategory()['Processors'];
        $traders = $this->getCategory()['Traders'];
        $partners = $this->getCategory()['Partners'];
        $staff = $this->getCategory()['Staff'];

        return [
            'Total' => $staff + $farmers + $processors + $traders + $partners,
            'Cassava' => $cassava,
            'Potato' => $potato,
            'Sweet potato' => $sweetPotato,
            'Farmers' => $farmers,
            'Processors' => $processors,
            'Traders' => $traders,
            'Partner' => $partners,
            'Staff' => $staff,
        ];
    }
}
