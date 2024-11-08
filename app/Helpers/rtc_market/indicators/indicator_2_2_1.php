<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;


class indicator_2_2_1
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

        $indicator = Indicator::where('indicator_name', 'Number of private sector actors involved in production of RTC certified seed')->where('indicator_no', '2.2.1')->first();

        $query = SubmissionReport::query()->where('indicator_id', $indicator->id)->where('status', 'approved');

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


    public function builderFarmer(): Builder
    {
        $query = RtcProductionFarmer::query()->with('followups')->where('rtc_production_farmers.status', 'approved');

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

        if ($this->organisation_id) {
            $query->where('organisation_id', $this->organisation_id);
        }

        return $query;
    }

    public function builderProcessor(): Builder
    {
        $query = RtcProductionProcessor::query()->with('followups')->where('rtc_production_processors.status', 'approved');

        if ($this->reporting_period && $this->financial_year) {
            $submissionPeriod = SubmissionPeriod::where('month_range_period_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year)->pluck('id')->toArray();
            if (!empty($submissionPeriod)) {
                $batchUuids = Submission::whereIn('period_id', $submissionPeriod)->pluck('batch_no')->toArray();
                if (!empty($batchUuids)) {
                    $query->orWhereIn('uuid', $batchUuids);
                } else {
                    return $query->whereIn('uuid', []); // Empty result if no valid batch UUIDs
                }
            }
        }

        return $query;
    }
    public function getTotals()
    {

        $builder = $this->builder()->get();

        $indicator = Indicator::where('indicator_name', 'Number of private sector actors involved in production of RTC certified seed')->where('indicator_no', '2.2.1')->first();
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

    public function findCropCount()
    {
        // Count for Cassava
        $cassavaTotal = $this->builderFarmer()
            ->where('enterprise', '=', 'Cassava')
            ->where('sector', '=', 'Private')
            ->where('group', '=', 'Seed multiplier')
            ->count();

        // Count for Sweet potato
        $sweetPotatoTotal = $this->builderFarmer()
            ->where('enterprise', '=', 'Sweet potato')
            ->where('sector', '=', 'Private')
            ->where('group', '=', 'Seed multiplier')
            ->count();

        // Count for Potato
        $potatoTotal = $this->builderFarmer()
            ->where('enterprise', '=', 'Potato')
            ->where('sector', '=', 'Private')
            ->where('group', '=', 'Seed multiplier')
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
        $total = $crop['cassava'] + $crop['sweet_potato'] + $crop['potato'];
        return [
            'Total' => $total,
            'Cassava' => $crop['cassava'],
            'Sweet potato' => $crop['sweet_potato'],
            'Potato' => $crop['potato'],
        ];
    }
}
