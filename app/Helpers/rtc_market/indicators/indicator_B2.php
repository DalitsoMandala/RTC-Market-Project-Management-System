<?php



namespace App\Helpers\rtc_market\indicators;

use App\Models\Submission;
use App\Models\SubmissionPeriod;
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

        $query = Submission::query()->where('batch_type', 'aggregate')->where('status', 'approved');

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

    public function getAggregateTotal()
    {

        // Fetch submissions with specific conditions
        $submissions = $this->builder()
            ->get();

        // Define keys to sum from the JSON data
        $keysToSum = [
            "Raw",
            "Total",
            "Potato",
            "Cassava",
            "Processed",
            "Sweet potato",
            "Formal exports",
            "Informal exports",
            "Financial value ($)",
            "Volume (Metric Tonnes)",
        ];

        // Initialize totals array
        $totals = array_fill_keys($keysToSum, 0);


        // Iterate through each submission
        foreach ($submissions as $submission) {
            $data = json_decode($submission->data, true);

            // Sum up the specified keys
            foreach ($keysToSum as $key) {
                if (isset($data[$key])) {
                    $totals[$key] += (float) $data[$key]; // Convert to float in case of numeric values
                }
            }
        }

        return $totals;

    }

    public function getDisaggregations()
    {

        return $this->getAggregateTotal();

    }



}