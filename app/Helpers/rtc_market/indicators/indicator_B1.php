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
        if ($this->reporting_period && $this->financial_year) {
            // Filter by period and year
            $data = $query->where('period_month_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year);

            // If no data is found, force an empty result but don't exit early
            if (!$data->exists()) {
                $query->whereIn('id', []); // Empty result filter
            } else {
                $query = $data; // If data exists, use the filtered query
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
        if ($this->reporting_period && $this->financial_year) {
            // Filter by period and year
            $data = $query->where('period_month_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year);

            // If no data is found, force an empty result but don't exit early
            if (!$data->exists()) {
                $query->whereIn('id', []); // Empty result filter
            } else {
                $query = $data; // If data exists, use the filtered query
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
        if ($this->reporting_period && $this->financial_year) {
            // Filter by period and year
            $data = $query->where('period_month_id', $this->reporting_period)
                ->where('financial_year_id', $this->financial_year);

            // If no data is found, force an empty result but don't exit early
            if (!$data->exists()) {
                $query->whereIn('id', []); // Empty result filter
            } else {
                $query = $data; // If data exists, use the filtered query
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
        $data = $this->calculations();
        $collect = collect($data);
        return [
            'cassava' => $collect->pluck('cassava')->sum(),
            'potato' => $collect->pluck('potato')->sum(),
            'sweet_potato' => $collect->pluck('sweet_potato')->sum(),
        ];
    }
    public function calculations()
    {
        // Initialize crop values
        $cassavaFarmerValue = 0;
        $potatoFarmerValue = 0;
        $swPotatoFarmerValue = 0;
        $cassavaProcessorValue = 0;
        $potatoProcessorValue = 0;
        $swPotatoProcessorValue = 0;

        // Get the project and financial years
        $project = Project::where('name', $this->projectName)->first();
        $financialYears = $project->financialYears()->pluck('id');

        $countData = [];

        // Get farmer data with followups
        $farmers = $this->Farmerbuilder()->with('followups')->get();

        // Get processor data with followups
        $processors = $this->Processorbuilder()->with('followups')->get();

        // Loop through the financial years
        foreach ($financialYears as $year_id) {
            // Farmers' cassava value
            $cassavaFarmerValue = $farmers->where('enterprise', 'Cassava')
                ->where('financial_year_id', $year_id)
                ->sum(function ($farmer) {
                    $farmerValue = $farmer->prod_value_previous_season_usd_value;
                    $followUpValue = optional($farmer->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
                    return $farmerValue + $followUpValue;
                });

            // Farmers' potato value
            $potatoFarmerValue = $farmers->where('enterprise', 'Potato')
                ->where('financial_year_id', $year_id)
                ->sum(function ($farmer) {
                    $farmerValue = $farmer->prod_value_previous_season_usd_value;
                    $followUpValue = optional($farmer->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
                    return $farmerValue + $followUpValue;
                });

            // Farmers' sweet potato value
            $swPotatoFarmerValue = $farmers->where('enterprise', 'Sweet potato')
                ->where('financial_year_id', $year_id)
                ->sum(function ($farmer) {
                    $farmerValue = $farmer->prod_value_previous_season_usd_value;
                    $followUpValue = optional($farmer->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
                    return $farmerValue + $followUpValue;
                });

            // Processors' cassava value
            $cassavaProcessorValue = $processors->where('enterprise', 'Cassava')
                ->where('financial_year_id', $year_id)
                ->sum(function ($processor) {
                    $processorValue = $processor->prod_value_previous_season_usd_value;
                    $followUpValue = optional($processor->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
                    return $processorValue + $followUpValue;
                });

            // Processors' potato value
            $potatoProcessorValue = $processors->where('enterprise', 'Potato')
                ->where('financial_year_id', $year_id)
                ->sum(function ($processor) {
                    $processorValue = $processor->prod_value_previous_season_usd_value;
                    $followUpValue = optional($processor->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
                    return $processorValue + $followUpValue;
                });

            // Processors' sweet potato value
            $swPotatoProcessorValue = $processors->where('enterprise', 'Sweet potato')
                ->where('financial_year_id', $year_id)
                ->sum(function ($processor) {
                    $processorValue = $processor->prod_value_previous_season_usd_value;
                    $followUpValue = optional($processor->followups)->sum('prod_value_previous_season_usd_value') ?? 0;
                    return $processorValue + $followUpValue;
                });

            // Aggregate all values together for the final crop count for this year
            $countData[$year_id] = [
                'cassava' => $cassavaFarmerValue + $cassavaProcessorValue,
                'potato' => $potatoFarmerValue + $potatoProcessorValue,
                'sweet_potato' => $swPotatoFarmerValue + $swPotatoProcessorValue,
                'total' => $cassavaFarmerValue + $cassavaProcessorValue + $potatoFarmerValue + $potatoProcessorValue + $swPotatoFarmerValue + $swPotatoProcessorValue

            ];
            $indicator = $this->findIndicator();
            if ($indicator) {

                $baseline = IndicatorTarget::where('indicator_id', $indicator->id)->where('financial_year_id', $year_id)->first();
                if ($baseline) {
                    $baselineValue = $baseline->baseline_value;
                    $annualValue = $countData[$year_id]['total'] == 0 ? 0 : $countData[$year_id]['total'];
                    $percentage = 0;
                    if (!$annualValue == 0) {


                        $percentageIncrease = new IncreasePercentage($annualValue, $baselineValue);
                        $percentage = $percentageIncrease->percentage();
                    }



                    $countData[$year_id]['percentage'] = $percentage;
                    $countData[$year_id]['target_value'] = $baseline->target_value;
                    $countData[$year_id]['calculated_percentage'] = ($baseline->target_value / 100) * $percentage;
                }
            }


        }

        // Debug output for checking


        return $countData;
    }

    public function findIndicator()
    {
        //Find baseline value
        $indicatorId = IndicatorClass::where('class', __CLASS__)->first();
        if ($indicatorId) {
            $indicator = Indicator::find($indicatorId->indicator_id);
            return $indicator;
        }

        return;
    }


    public function findTotal()
    {
        if ($this->financial_year) {
            $calculations = $this->calculations();
            $collect = collect($calculations[$this->financial_year]);
            $total = $collect->sum('calculated_percentage');

            if ($this->lop == 0) {
                return 0;
            }

            return ($this->lop / 100) * $total;
        } else {
            $calculations = $this->calculations();
            $collect = collect($calculations);
            $total = $collect->sum('calculated_percentage');

            if ($this->lop == 0) {
                return 0;
            }

            return ($this->lop / 100) * $total;

        }


    }


    public function getDisaggregations()
    {

        $total = $this->findTotal();
        $crop = $this->findCropCount();
        return [
            'Total(% Percentage)' => (int) $total,
            'Cassava' => (int) $crop['cassava'],
            'Sweet potato' => (int) $crop['sweet_potato'],
            'Potato' => (int) $crop['potato'],
        ];
    }
}
