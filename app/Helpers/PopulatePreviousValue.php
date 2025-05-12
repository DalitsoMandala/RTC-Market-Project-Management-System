<?php

namespace App\Helpers;

use App\Models\Indicator;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SystemReportData;
use App\Models\PercentageIncreaseIndicator;
use App\Models\ReportingPeriodMonth;

class PopulatePreviousValue
{
    public function start()
    {
        $financialYears = FinancialYear::where('project_id', 1)->orderBy('number')->get();
        $indicators = Indicator::with('disaggregations', 'organisation')->whereHas('disaggregations', function ($query) {
            $query->where('name', 'Total (% Percentage)');
        })->get();

        foreach ($indicators as $indicator) {
            // Initialize each organization's previous value with the baseline
            $previousValues = [];


            foreach ($indicator->organisation as $organisation) {
                $previousValues[$organisation->id] = $indicator->baseline->baseline_value;
            }




            foreach ($financialYears as $financialYear) {
                foreach ($indicator->organisation as $organisation) {
                    // Retrieve the previous value specific to this organization
                    $previousValue = $previousValues[$organisation->id];

                    // Calculate annual value for this financial year and organization
                    $annualValue = $this->getAnnualValue($financialYear, $indicator, $previousValue, $organisation, 'Total (% Percentage)');
                    $growthPercentage = $this->calculateGrowthPercentage($annualValue, $previousValue);


                    // Save or update previous value and growth percentage for the organization
                    $this->saveOrUpdatePreviousValue($financialYear, $indicator, $annualValue, $growthPercentage, $organisation, 'Total (% Percentage)');

                    // Update the previous value for this organization for the next financial year
                    $previousValues[$organisation->id] = $annualValue;
                }
            }
        }
    }



    protected function getAnnualValue($financialYear, $indicator, $previousValue, $organisation, $disaggregation_name)
    {
        // For the first year, return the baseline as the annual value
        if ($financialYear->number == 1) {
            return $previousValue;
        }

        $reportIds = SystemReport::where('financial_year_id', $financialYear->id)
            ->where('project_id', 1)
            ->where('organisation_id', $organisation->id)
            ->where('indicator_id', $indicator->id)
            ->pluck('id');

        $data = SystemReportData::whereIn('system_report_id', $reportIds)->get();


        // Calculate the annual value based on the indicator's type
        switch ($indicator->indicator_name) {
            case 'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities':



                return $this->sumDisaggregations($data, $disaggregation_name, [
                    'Cassava',
                    'Potato',
                    'Sweet potato'
                ]);

            case 'Percentage increase in value of formal RTC exports':

                $temp = $this->sumDisaggregations($data, $disaggregation_name, [
                    '(Formal) Cassava',
                    '(Formal) Potato',
                    '(Formal) Sweet potato',


                ]);



                return $this->sumDisaggregations($data, $disaggregation_name, [
                    '(Formal) Cassava',
                    '(Formal) Potato',
                    '(Formal) Sweet potato',

                ]);

            case 'Percentage of value ($) of formal RTC imports substituted through local production':
                return $this->sumDisaggregations($data, $disaggregation_name, [
                    '(Formal) Cassava',
                    '(Formal) Potato',
                    '(Formal) Sweet potato'
                ]);

            case 'Percentage Increase in the volume of RTC produced':
            case 'Percentage increase in adoption of new RTC technologies':
            case 'Percentage seed multipliers with formal registration':
                //  case 'Percentage business plans for the production of different classes of RTC seeds that are executed':
            case 'Percentage increase in households consuming RTCs as the main foodstuff (OC)':
                return $this->sumDisaggregations($data, $disaggregation_name, [
                    'Cassava',
                    'Potato',
                    'Sweet potato'
                ]);

            case 'Percentage increase in RTC investment':
                return $this->sumDisaggregations($data, $disaggregation_name, [
                    '(Formal) Cassava',
                    '(Formal) Potato',
                    '(Formal) Sweet potato'
                ]);

            case 'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)':
                return $data->where('name', 'Total')->sum('value');

            default:
                return 0; // Default to 0 if no matching case
        }
    }

    protected function sumDisaggregations($data, $disaggregation_name, array $categories)
    {
        if ($disaggregation_name == 'Total (% Percentage)') {
            return $data->whereIn('name', $categories)->sum('value');
        }

        return $data->where('name', $disaggregation_name)->sum('value');
    }

    protected function calculateGrowthPercentage($annualValue, $baseline)
    {

        if ($annualValue == 0 || $baseline == 0) {
            return 0; // Avoid division by zero
        }
        return round((($annualValue - $baseline) / $annualValue) * 100, 2);
    }


    protected function saveOrUpdatePreviousValue($financialYear, $indicator, $annualValue, $growthPercentage, $organisation, $disaggregation_name)
    {
        $unspecified = ReportingPeriodMonth::where('type', 'UNSPECIFIED')->first();
        // Identify the last reporting period for the financial year
        $lastReportingPeriod = SystemReport::where('financial_year_id', $financialYear->id)
            ->where('project_id', 1)
            ->where('indicator_id', $indicator->id)
            ->where('organisation_id', $organisation->id)
            ->where('reporting_period_id', $unspecified->id) // unspecified
            //      ->orderByDesc('reporting_period_id') // Get the latest reporting period
            ->pluck('reporting_period_id')
            ->first(); // Get only the last period

        if (!$lastReportingPeriod) {
            return; // No report exists, exit early
        }

        // Update PercentageIncreaseIndicator
        PercentageIncreaseIndicator::updateOrCreate(
            [
                'financial_year_id' => $financialYear->id,
                'indicator_id' => $indicator->id,
                'organisation_id' => $organisation->id,
                'name' => $disaggregation_name
            ],
            [
                'total_value' => $annualValue,
                'growth_percentage' => $growthPercentage,
            ]
        );

        // Get reports only for the last reporting period
        $reportIds = SystemReport::where('financial_year_id', $financialYear->id)
            ->where('project_id', 1)
            ->where('indicator_id', $indicator->id)
            ->where('organisation_id', $organisation->id)
            ->where('reporting_period_id', $lastReportingPeriod) // Filter for last period only
            ->pluck('id');

        // Update SystemReportData only for last reporting period
        $data = SystemReportData::whereIn('system_report_id', $reportIds)
            ->where('name', $disaggregation_name)
            ->get();

        foreach ($data as $item) {
            $item->update([
                'value' => $growthPercentage
            ]);
        }
    }
}
