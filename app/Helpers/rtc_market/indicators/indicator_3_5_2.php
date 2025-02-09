<?php

namespace App\Helpers\rtc_market\indicators;

use App\Traits\FilterableQuery;

use Illuminate\Support\Facades\Log;
use App\Models\Indicator;
use Illuminate\Support\Facades\DB;
use App\Helpers\IncreasePercentage;
use App\Models\SchoolRtcConsumption;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log as Logger;


class indicator_3_5_2
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;



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
        $query = HouseholdRtcConsumption::query()->where('status', 'approved');




        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }


        return $this->applyFilters($query);
    }


    public function builderSchool(): Builder
    {
        $query = SchoolRtcConsumption::query()->where('status', 'approved');




        // if ($this->organisation_id && $this->target_year_id) {
        //     $data = $query->where('organisation_id', $this->organisation_id)->where('financial_year_id', $this->target_year_id);
        //     $query = $data;

        // } else
        //     if ($this->organisation_id && $this->target_year_id == null) {
        //         $data = $query->where('organisation_id', $this->organisation_id);
        //         $query = $data;

        //     }


        return $query;
    }
    public function getFrequency()
    {
        // Optimized household query: using `value()` instead of `first()->toArray()` for a single aggregate.
        $householdTotal = $this->builder()
            ->select(DB::raw('SUM(rtc_consumption_frequency) as Total'))
            ->value('Total'); // Directly get the summed value.


        // $totalFrequencySum = 0; // Initialize the total frequency sum
        // $this->builderSchool()
        //     ->select(
        //         'school_name',
        //         DB::raw('COUNT(CASE WHEN crop IN ("Cassava", "Potato", "Sweet Potato") THEN 1 END) as total_frequency')
        //     )
        //     ->groupBy('school_name')
        //     ->orderBy('school_name') // Add orderBy to ensure consistent chunking
        //     ->chunk(100, function ($schoolFrequencies) use (&$totalFrequencySum) {
        //         // Loop through each chunk of results
        //         foreach ($schoolFrequencies as $school) {
        //             $totalFrequencySum += $school->total_frequency; // Add the frequency to the total sum
        //         }
        //     });
        return [
            'Total' => $householdTotal
        ];
    }


    public function findIndicator()
    {
        $indicator = Indicator::where('indicator_name', 'Frequency of RTC consumption by households per week (OC)')->where('indicator_no', '3.5.2')->first();
        return $indicator ?? Logger::error('Indicator not found');
    }


    public function getDisaggregations()
    {
        $subTotal = $this->getFrequency()['Total'];
        // Retrieve the indicator
        $indicator = $this->findIndicator();

        // Get the baseline value, defaulting to 0 if the indicator or baseline doesn't exist
        $baseline = $indicator->baseline->baseline_value ?? 0;

        // Calculate the percentage increase based on the subtotal and baseline
        $percentageIncrease = new IncreasePercentage($subTotal, $baseline);
        $finalTotalPercentage = $percentageIncrease->percentage();

        $this->getFrequency();
        return [
            'Total' => $finalTotalPercentage,
        ];
    }
}
