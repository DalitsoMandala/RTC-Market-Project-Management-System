<?php



namespace App\Helpers\rtc_market\indicators;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;


class Indicator_B2
{
    public function __construct($start_date = null, $end_date = null)
    {

        $this->start_date = $start_date;
        $this->end_date = $end_date;

    }
    public function builder(): Builder
    {

        return Submission::query()->where('batch_type', 'aggregate')->where('status', 'approved');

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