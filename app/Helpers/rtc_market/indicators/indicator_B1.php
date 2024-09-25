<?php

namespace App\Helpers\rtc_market\indicators;

use App\Helpers\IncreasePercentage;
use App\Models\FinancialYear;
use Log;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\IndicatorClass;
use App\Models\IndicatorTarget;
use App\Models\Project;
use App\Models\SubmissionPeriod;
use App\Models\RpmFarmerFollowUp;
use App\Models\RtcProductionFarmer;
use App\Models\RpmProcessorFollowUp;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;


class indicator_B1
{
    protected $disaggregations = [];
    protected $start_date;
    protected $end_date;
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $target_year_id;

    protected $projectName = 'RTC MARKET';

    protected $lop = 30;
    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $target_year_id = null)
    {



        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        //$this->project = $project;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;
    }
    public function Farmerbuilder(): Builder
    {

        $query = RtcProductionFarmer::query()->with('followups')->where('status', 'approved');



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

    public function FarmerFollowupbuilder(): Builder
    {
        $farmer = $this->Farmerbuilder()->pluck('id');


        $query = RpmFarmerFollowUp::query()->where('status', 'approved')->whereIn('rpm_farmer_id', $farmer);


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

        $query = RtcProductionProcessor::query()->with('followups')->where('status', 'approved');

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

    public function ProcessorFollowupbuilder(): Builder
    {

        $query = RpmProcessorFollowUp::query()->where('status', 'approved');


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


    public function findCropCount()
    {
        $data = collect($this->calculations());

        if ($this->financial_year) {
            $yearData = $data[$this->financial_year];

            return [
                'cassava' => $yearData['cassava'],
                'potato' => $yearData['potato'],
                'sweet_potato' => $yearData['sweet_potato'],
            ];
        }

        return [
            'cassava' => $data->pluck('cassava')->sum(),
            'potato' => $data->pluck('potato')->sum(),
            'sweet_potato' => $data->pluck('sweet_potato')->sum(),
        ];
    }

    public function calculations()
    {
        $project = Project::where('name', $this->projectName)->first();
        $financialYears = $project->financialYears()->pluck('id');
        $cropData = [
            'Cassava' => ['farmerValue' => 0, 'processorValue' => 0],
            'Potato' => ['farmerValue' => 0, 'processorValue' => 0],
            'Sweet potato' => ['farmerValue' => 0, 'processorValue' => 0],
        ];

        $countData = [];

        foreach ($financialYears as $year_id) {
            // Chunking farmers
            $this->Farmerbuilder()->chunk(1000, function ($farmers) use (&$cropData, $year_id) {
                foreach (['Cassava', 'Potato', 'Sweet potato'] as $crop) {
                    $cropData[$crop]['farmerValue'] += $this->calculateCropValue($farmers, $crop, $year_id);
                }
            });

            // Chunking processors
            $this->Processorbuilder()->chunk(1000, function ($processors) use (&$cropData, $year_id) {
                foreach (['Cassava', 'Potato', 'Sweet potato'] as $crop) {
                    $cropData[$crop]['processorValue'] += $this->calculateCropValue($processors, $crop, $year_id);
                }
            });

            // Calculate totals for each crop
            $cassava = $cropData['Cassava']['farmerValue'] + $cropData['Cassava']['processorValue'];
            $potato = $cropData['Potato']['farmerValue'] + $cropData['Potato']['processorValue'];
            $sweetPotato = $cropData['Sweet potato']['farmerValue'] + $cropData['Sweet potato']['processorValue'];
            $total = $cassava + $potato + $sweetPotato;

            $countData[$year_id] = [
                'cassava' => $cassava,
                'potato' => $potato,
                'sweet_potato' => $sweetPotato,
                'total' => $total,
            ];

            $this->calculateIndicatorData($countData[$year_id], $year_id, $total);
        }

        return $countData;
    }


    private function calculateCropValue($data, $crop, $year_id)
    {
        return $data->where('enterprise', $crop)
            ->where('financial_year_id', $year_id)
            ->sum(function ($item) {
                return $item->prod_value_previous_season_usd_value + optional($item->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
            });
    }

    private function calculateIndicatorData(&$countDataYear, $year_id, $total)
    {
        $indicator = $this->findIndicator();
        if ($indicator) {
            $baseline = IndicatorTarget::where('indicator_id', $indicator->id)->where('financial_year_id', $year_id)->first();
            if ($baseline) {
                $baselineValue = $baseline->baseline_value;
                $percentage = ($total > 0) ? (new IncreasePercentage($total, $baselineValue))->percentage() : 0;

                $countDataYear['percentage'] = $percentage;
                $countDataYear['target_value'] = $baseline->target_value;
                $countDataYear['calculated_percentage'] = ($baseline->target_value / 100) * $percentage;
            }
        }
    }

    public function findTotal()
    {
        $calculations = $this->calculations();
        $arrayData = [];
        $data = 0;
        if ($this->financial_year) {
            $temp = collect($calculations[$this->financial_year]);
            $data = $temp['calculated_percentage'];
            $total = $data;
            return $this->lop ? ($this->lop / 100) * $total : 0;
        }
        $arrayData = collect($calculations);

        $total = $arrayData->sum('calculated_percentage');

        return $this->lop ? ($this->lop / 100) * $total : 0;
    }
    public function findIndicator()
    {
        $indicatorId = IndicatorClass::where('class', __CLASS__)->first();
        return $indicatorId ? Indicator::find($indicatorId->indicator_id) : null;
    }
    public function getDisaggregations()
    {
        $total = $this->findTotal();
        $crop = $this->findCropCount();

        return [
            'Total(% Percentage)' => round($total, 2),
            'Cassava' => round($crop['cassava'], 2),
            'Sweet potato' => round($crop['sweet_potato'], 2),
            'Potato' => round($crop['potato'], 2),
        ];
    }



}
