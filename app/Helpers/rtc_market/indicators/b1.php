<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\RpmFarmerFollowUp;
use App\Models\RpmProcessorFollowUp;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use Illuminate\Contracts\Database\Eloquent\Builder;

class B1
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;

    public function __construct($start_date = null, $end_date = null)
    {

        $this->start_date = $start_date;
        $this->end_date = $end_date;

    }

    public function Farmerbuilder(): Builder
    {

        $query = RtcProductionFarmer::query();

        if ($this->start_date || $this->end_date) {

            $query->where(function ($query) {
                if ($this->start_date) {
                    $query->where('created_at', '>=', $this->start_date);
                }
                if ($this->end_date) {
                    $query->where('created_at', '<=', $this->end_date);
                }
            });
        }

        return $query;

    }

    public function FarmerFollowupbuilder(): Builder
    {

        $query = RpmFarmerFollowUp::query();

        if ($this->start_date || $this->end_date) {

            $query->where(function ($query) {
                if ($this->start_date) {
                    $query->where('created_at', '>=', $this->start_date);
                }
                if ($this->end_date) {
                    $query->where('created_at', '<=', $this->end_date);
                }
            });
        }

        return $query;

    }

    public function Processorbuilder(): Builder
    {

        $query = RtcProductionProcessor::query();

        if ($this->start_date || $this->end_date) {

            $query->where(function ($query) {
                if ($this->start_date) {
                    $query->where('created_at', '>=', $this->start_date);
                }
                if ($this->end_date) {
                    $query->where('created_at', '<=', $this->end_date);
                }
            });
        }

        return $query;

    }

    public function ProcessorFollowupbuilder(): Builder
    {

        $query = RpmProcessorFollowUp::query();

        if ($this->start_date || $this->end_date) {

            $query->where(function ($query) {
                if ($this->start_date) {
                    $query->where('created_at', '>=', $this->start_date);
                }
                if ($this->end_date) {
                    $query->where('created_at', '<=', $this->end_date);
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

        // Query the second table (assuming a different builder)
        $farmerTotalFollowup = $this->FarmerFollowupbuilder() // Corrected to use a different builder
            ->selectRaw('
                SUM(CAST(JSON_EXTRACT(total_production_value_previous_season, "$.total") AS UNSIGNED)) as total
            ')
            ->first();

        if ($farmerTotalFollowup) {
            // Convert the result to an array and access the total value
            $farmerTotalFollowupArray = $farmerTotalFollowup->toArray();
            $total2 = $farmerTotalFollowupArray['total'];
        }

        // Sum the totals from both queries
        $combinedTotal = $total + $total2;

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