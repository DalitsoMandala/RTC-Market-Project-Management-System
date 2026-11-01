<?php
namespace App\Helpers;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\PercentageIncreaseIndicator;
use App\Models\ReportingPeriodMonth;
use App\Models\SystemReport;
use App\Models\SystemReportData;

functionpublic start( $projectId)
{
    $financialYears = FinancialYear::where('project_id', $projectId)->orderBy('number')->get();
    $indicators     = Indicator::with(['disaggregations', 'organisation', 'baseline'])
        ->whereHas('disaggregations', fn($q) => $q->where('name', 'Total (% Percentage)'))
        ->get();

    $crops = CoreFunctions::getCropsWithNull();

    foreach ($indicators as $indicator) {
        $baselineValue = (float) ($indicator->baseline->baseline_value ?? 0);

        // tracker structure: $previousValues[org_id][crop_name]
        $previousValues = [];

        foreach ($financialYears as $financialYear) {
            foreach ($crops as $crop) {
                $yearTotalValue     = 0;
                $yearTotalPrevValue = 0;

                foreach ($indicator->organisation as $organisation) {
                    $orgId = $organisation->id;

                    // Initialize baseline if first time seeing this Org/Crop
                    if (! isset($previousValues[$orgId][$crop])) {
                        $previousValues[$orgId][$crop] = $baselineValue;
                    }

                    $prevVal = $previousValues[$orgId][$crop];

                    // 1. Get current annual value (Averaged if % indicator)
                    $annualValue = $this->getAnnualValue($financialYear, $indicator, $prevVal, $organisation, 'Total (% Percentage)', $crop, $projectId);

                    // 2. Calculate Growth for the Individual Org
                    $growthPercentage = $this->calculateGrowthPercentage($annualValue, $prevVal);

                    // 3. Save individual Org record
                    $this->saveOrUpdatePreviousValue($financialYear, $indicator, $annualValue, $growthPercentage, $organisation, 'Total (% Percentage)', $crop, $projectId);

                    // 4. Accumulate for the "Global" sum
                    $yearTotalValue     += $annualValue;
                    $yearTotalPrevValue += $prevVal;

                    // 5. Update tracker for next year
                    $previousValues[$orgId][$crop] = $annualValue;
                }

                // --- CALCULATE GLOBAL TOTAL ---
                // This uses the "Aggregate Growth" method to avoid outlier distortion
                $globalGrowth = $this->calculateGrowthPercentage($yearTotalValue, $yearTotalPrevValue);

                // Save the "Global" record with NULL for organisation_id
                $this->saveOrUpdatePreviousValue(
                    $financialYear,
                    $indicator,
                    $yearTotalValue,
                    $globalGrowth,
                    null, // NULL signifies Global/Project Total
                    'Total (% Percentage)',
                    $crop,
                    $projectId
                );
            }
        }
    }
}

functionprotected getAnnualValue( $financialYear, $indicator, $previousValue, $organisation, $disaggregation_name, $crop, $projectId)
{
    if ($financialYear->number == 1) {
        return $previousValue;
    }

    $reportIds = SystemReport::where([
        'financial_year_id' => $financialYear->id,
        'project_id'        => $projectId,
        'organisation_id'   => $organisation->id,
        'indicator_id'      => $indicator->id,
        'crop'              => $crop,
    ])->pluck('id');

    if ($reportIds->isEmpty()) {
        // Return 0 if no reports, representing a drop to zero for that year
        return 0;
    }

    $data       = SystemReportData::whereIn('system_report_id', $reportIds)->get();
    $categories = self::INDICATOR_CATEGORIES[$indicator->indicator_name] ?? null;

    if (! $categories) {
        return 0;
    }

    // Use Average for percentages, Sum for others
    if ($disaggregation_name === 'Total (% Percentage)') {
        return $data->whereIn('name', $categories)
            ->where('value', '>', 0)
            ->avg('value') ?? 0;
    }

    return $data->whereIn('name', $categories)->sum('value');
}

functionprotected calculateGrowthPercentage( $annualValue, $baseline)
{
    if ($baseline <= 0) {
        // If we have an annual value but no baseline, growth is 100%
        // If both are 0, growth is 0.
        return $annualValue > 0 ? 100 : 0;
    }

    return round((($annualValue - $baseline) / $baseline) * 100, 2);
}

functionprotected saveOrUpdatePreviousValue( $financialYear, $indicator, $annualValue, $growthPercentage, $organisation, $disaggregation_name, $crop, $projectId)
{
    $unspecified = ReportingPeriodMonth::where('type', 'UNSPECIFIED')->first();
    if (! $unspecified) {
        return;
    }

    // Determine the org ID (null if we are processing the Global Total)
    $orgId = $organisation ? $organisation->id : null;

    PercentageIncreaseIndicator::updateOrCreate(
        [
            'financial_year_id' => $financialYear->id,
            'indicator_id'      => $indicator->id,
            'organisation_id'   => $orgId,
            'name'              => $disaggregation_name,
        ],
        [
            'total_value'       => $annualValue,
            'growth_percentage' => $growthPercentage,
        ]
    );

    // Only update SystemReportData for actual organisations (skip Global null)
    // if ($orgId) {
    //     $reportIds = SystemReport::where([
    //         'financial_year_id'   => $financialYear->id,
    //         'project_id'          => $projectId,
    //         'indicator_id'        => $indicator->id,
    //         'organisation_id'     => $orgId,
    //         'crop'                => $crop,
    //         'reporting_period_month_id' => $unspecified->id,
    //     ])->pluck('id');

    //     if ($reportIds->isNotEmpty()) {
    //         SystemReportData::whereIn('system_report_id', $reportIds)
    //             ->where('name', $disaggregation_name)
    //             ->update(['value' => $growthPercentage]);
    //     }
    // }
}
}
