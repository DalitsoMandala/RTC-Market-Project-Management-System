<?php

namespace App\Helpers;

use App\Models\Indicator;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SystemReportData;
use App\Models\IndicatorPreviousValue;
use App\Models\PercentageIncreaseIndicator;

class PopulatePreviousValue
{
    public function start()
    {
        $financialYears = FinancialYear::where('project_id', 1)->orderBy('number')->get();
        $indicators = Indicator::with('disaggregations', 'organisation')->whereHas('disaggregations', function ($query) {
            $query->where('name', 'Total (% Percentage)');
        })->get();

        foreach ($indicators as $indicator) {
            $previousValue = $indicator->baseline->baseline_value;

            foreach ($financialYears as $financialYear) {
                $organisations = $indicator->organisation;

                foreach ($organisations as $organisation) {
                    $annualValue = $this->getAnnualValue($financialYear, $indicator, $previousValue, $organisation);
                    $growthPercentage = $this->calculateGrowthPercentage($annualValue, $previousValue);

                    $this->saveOrUpdatePreviousValue($financialYear, $indicator, $annualValue, $growthPercentage, $organisation);

                    $previousValue = $annualValue; // Update for next year
                }
            }
        }
    }

    protected function getAnnualValue($financialYear, $indicator, $previousValue, $organisation)
    {
        if ($financialYear->number == 1) {
            return $previousValue;
        }

        $report = SystemReport::where('financial_year_id', $financialYear->id)
            ->where('project_id', 1)
            ->where('indicator_id', $indicator->id)
            ->get();

        $data = SystemReportData::whereIn('system_report_id', $report->pluck('id'))->get();

        switch ($indicator->indicator_name) {
            case 'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities':
                return $data->where('name', 'Cassava')->sum('value') +
                    $data->where('name', 'Potato')->sum('value') +
                    $data->where('name', 'Sweet potato')->sum('value');

            case 'Percentage increase in value of formal RTC exports':
                return $data->where('name', '(Formal) Cassava')->sum('value') +
                    $data->where('name', '(Formal) Potato')->sum('value') +
                    $data->where('name', '(Formal) Sweet potato')->sum('value') +
                    $data->where('name', '(Informal) Cassava')->sum('value') +
                    $data->where('name', '(Informal) Potato')->sum('value') +
                    $data->where('name', '(Informal) Sweet potato')->sum('value');

            case 'Percentage of value ($) of formal RTC imports substituted through local production':
                return $data->where('name', '(Formal) Cassava')->sum('value') +
                    $data->where('name', '(Formal) Potato')->sum('value') +
                    $data->where('name', '(Formal) Sweet potato')->sum('value');

            case 'Percentage Increase in the volume of RTC produced':
            case 'Percentage increase in adoption of new RTC technologies':
            case 'Percentage seed multipliers with formal registration':
            case 'Percentage business plans for the production of different classes of RTC seeds that are executed':
            case 'Percentage increase in households consuming RTCs as the main foodstuff (OC)':
                return $data->where('name', 'Cassava')->sum('value') +
                    $data->where('name', 'Potato')->sum('value') +
                    $data->where('name', 'Sweet potato')->sum('value');

            case 'Percentage increase in RTC investment':
                return $data->where('name', '(Formal) Cassava')->sum('value') +
                    $data->where('name', '(Formal) Potato')->sum('value') +
                    $data->where('name', '(Formal) Sweet potato')->sum('value');

            case 'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)':
                return $data->where('name', 'Total')->sum('value');

            default:
                return 0; // Default to 0 if no matching case
        }
    }

    protected function calculateGrowthPercentage($annualValue, $baseline)
    {
        if ($annualValue == 0) {
            return 0; // Avoid division by zero
        }
        return (($annualValue - $baseline) / $annualValue) * 100;
    }

    protected function saveOrUpdatePreviousValue($financialYear, $indicator, $annualValue, $growthPercentage, $organisation)
    {
        PercentageIncreaseIndicator::updateOrCreate(
            [
                'financial_year_id' => $financialYear->id,
                'indicator_id' => $indicator->id,
                'organisation_id' => $organisation->id,
            ],
            [
                'total_value' => $annualValue,
                'growth_percentage' => round($growthPercentage, 2),
            ]
        );


        $reportIds = SystemReport::where('financial_year_id', $financialYear->id)->where('project_id', 1)->where('indicator_id', $indicator->id)
            ->where('organisation_id', $organisation->id)
            ->pluck('id');

        SystemReportData::whereIn('system_report_id', $reportIds)
            ->where('name', 'Total (% Percentage)')
            ->update([
                'value' => round($growthPercentage, 2)
            ]);


    }
}
