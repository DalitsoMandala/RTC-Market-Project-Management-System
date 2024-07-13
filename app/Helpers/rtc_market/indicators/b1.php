<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\RpmFarmerFollowUp;
use App\Models\RpmProcessorFollowUp;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Illuminate\Contracts\Database\Eloquent\Builder;

class B1
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

        $query = RtcProductionFarmer::query();


        if ($this->reporting_period || $this->financial_year) {


            $query->where(function ($query) {
                if ($this->reporting_period) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->where('period_id', $this->reporting_period)->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());

                }
                if ($this->financial_year) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->pluck('period_id')->unique();
                    $periods = SubmissionPeriod::whereIn('id', $submissions->toArray())->where('financial_year_id', $this->financial_year)->pluck('id');
                    $submissions = Submission::where('period_id', $periods->toArray())->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());
                }
            });






        }



        return $query;

    }

    public function FarmerFollowupbuilder(): Builder
    {

        $query = RpmFarmerFollowUp::query();

        if ($this->reporting_period || $this->financial_year) {


            $query->where(function ($query) {
                if ($this->reporting_period) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->where('period_id', $this->reporting_period)->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());

                }
                if ($this->financial_year) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->pluck('period_id')->unique();
                    $periods = SubmissionPeriod::whereIn('id', $submissions->toArray())->where('financial_year_id', $this->financial_year)->pluck('id');
                    $submissions = Submission::where('period_id', $periods->toArray())->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());
                }
            });






        }

        return $query;

    }

    public function Processorbuilder(): Builder
    {

        $query = RtcProductionProcessor::query();
        if ($this->reporting_period || $this->financial_year) {


            $query->where(function ($query) {
                if ($this->reporting_period) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->where('period_id', $this->reporting_period)->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());

                }
                if ($this->financial_year) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->pluck('period_id')->unique();
                    $periods = SubmissionPeriod::whereIn('id', $submissions->toArray())->where('financial_year_id', $this->financial_year)->pluck('id');
                    $submissions = Submission::where('period_id', $periods->toArray())->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());
                }
            });






        }

        return $query;

    }

    public function ProcessorFollowupbuilder(): Builder
    {

        $query = RpmProcessorFollowUp::query();

        if ($this->reporting_period || $this->financial_year) {


            $query->where(function ($query) {
                if ($this->reporting_period) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->where('period_id', $this->reporting_period)->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());

                }
                if ($this->financial_year) {
                    $filterUuids = $query->pluck('uuid')->unique()->toArray();
                    $submissions = Submission::whereIn('batch_no', $filterUuids)->pluck('period_id')->unique();
                    $periods = SubmissionPeriod::whereIn('id', $submissions->toArray())->where('financial_year_id', $this->financial_year)->pluck('id');
                    $submissions = Submission::where('period_id', $periods->toArray())->pluck('batch_no');
                    $query->whereIn('uuid', $submissions->toArray());
                }
            });






        }

        return $query;

    }

    public function findCropCount()
    {
        // Query the first table
        $farmerCrop = $this->Farmerbuilder()
            ->selectRaw('
                SUM(CAST(JSON_EXTRACT(number_of_plantlets_produced, "$.potato") AS UNSIGNED)) as potato,
                SUM(CAST(JSON_EXTRACT(number_of_plantlets_produced, "$.cassava") AS UNSIGNED)) as cassava,
                SUM(CAST(JSON_EXTRACT(number_of_plantlets_produced, "$.sweet_potato") AS UNSIGNED)) as sweet_potato
            ')
            ->first();

        if ($farmerCrop) {
            $farmerCrop = $farmerCrop->toArray();
        } else {
            $farmerCrop = ['potato' => 0, 'cassava' => 0, 'sweet_potato' => 0];
        }

        // Query the second table
        $farmerCropFollowup = $this->FarmerFollowupbuilder()
            ->selectRaw('
                SUM(CAST(JSON_EXTRACT(number_of_plantlets_produced, "$.potato") AS UNSIGNED)) as potato,
                SUM(CAST(JSON_EXTRACT(number_of_plantlets_produced, "$.cassava") AS UNSIGNED)) as cassava,
                SUM(CAST(JSON_EXTRACT(number_of_plantlets_produced, "$.sweet_potato") AS UNSIGNED)) as sweet_potato
            ')
            ->first();

        if ($farmerCropFollowup) {
            $farmerCropFollowup = $farmerCropFollowup->toArray();
        } else {
            $farmerCropFollowup = ['potato' => 0, 'cassava' => 0, 'sweet_potato' => 0];
        }

        // Merge and sum the results
        $result = $this->mergeAndSumArrays([$farmerCrop, $farmerCropFollowup]);

        return $result;
    }
    public function findTotal()
    {
        // Initial totals
        $total = 0;
        $total2 = 0;

        // Query the first table
        $farmerTotal = $this->Farmerbuilder()
            ->selectRaw('
                SUM(CAST(JSON_EXTRACT(total_production_value_previous_season, "$.total") AS UNSIGNED)) as total
            ')
            ->first();

        if ($farmerTotal) {
            // Convert the result to an array and access the total value
            $farmerTotalArray = $farmerTotal->toArray();
            $total = $farmerTotalArray['total'];
        }

        // // Query the second table (assuming a different builder)
        // $farmerTotalFollowup = $this->FarmerFollowupbuilder() // Corrected to use a different builder
        //     ->selectRaw('
        //         SUM(CAST(JSON_EXTRACT(total_production_value_previous_season, "$.total") AS UNSIGNED)) as total
        //     ')
        //     ->first();

        // if ($farmerTotalFollowup) {
        //     // Convert the result to an array and access the total value
        //     $farmerTotalFollowupArray = $farmerTotalFollowup->toArray();
        //     $total2 = $farmerTotalFollowupArray['total'];
        // }

        // Sum the totals from both queries
        $combinedTotal = $total + 0;

        // Return the combined total
        return $combinedTotal;
    }

    public function mergeAndSumArrays($arrays)
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!isset($result[$key])) {
                    $result[$key] = 0;
                }
                $result[$key] += (float) $value;
            }
        }

        return $result;
    }

    public function getDisaggregations()
    {

        return [
            'Total' => $this->findTotal(),
            'Cassava' => $this->findCropCount()['cassava'],
            'Sweet potato' => $this->findCropCount()['sweet_potato'],
            'Potato' => $this->findCropCount()['potato'],
        ];

    }

}