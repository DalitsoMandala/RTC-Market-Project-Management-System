<?php

namespace App\Helpers\rtc_market\indicators;

use App\Models\RpmFarmerFollowUp;
use App\Models\RpmProcessorFollowUp;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Indicator_2_2_1
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

            ->where('sector', 'PRIVATE')
            ->first();

        if ($farmerCrop) {
            $farmerCrop = $farmerCrop->toArray();
        } else {
            $farmerCrop = ['potato' => 0, 'cassava' => 0, 'sweet_potato' => 0];
        }

        // Query the second table

        // Merge and sum the results
        $result = $this->mergeAndSumArrays([$farmerCrop]);

        return $result;
    }

    public function findTotal()
    {
        // Initial totals
        $total = 0;
        $total2 = 0;

        // Query the first table
        $farmerTotal = $this->Farmerbuilder()
            ->select([
                DB::raw('COUNT(*) AS total'),

            ])

            ->where('sector', 'PRIVATE')

            ->where('group', 'SEED MULTIPLIER')
            ->first();

        if ($farmerTotal) {
            // Convert the result to an array and access the total value
            $farmerTotalArray = $farmerTotal->toArray();
            $total = $farmerTotalArray['total'];
        }

        // Return the combined total
        return $total;
    }

    public function mergeAndSumArrays($arrays)
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!isset($result[$key])) {
                    $result[$key] = 0;
                }
                $result[$key] += (int) $value;
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
