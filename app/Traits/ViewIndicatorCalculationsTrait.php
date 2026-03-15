<?php

namespace App\Traits;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\SystemReport;
use App\Models\SystemReportData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait ViewIndicatorCalculationsTrait
{
    //

    public $rowId;
    public $data = [];
    public $indicator_no;
    public $indicator_id, $project_id;
    public $indicator_name;

    public $total;

    public $selectedProjectYear = [];

    public $projectYears = [];

    public $selectedOrganisation = 1;

    public $reportingPeriods = [];

    public $reporting_period;
    public $financial_year;
    public $organisation;
    public $crop;









    public function calculations()
{
    // 1. Fetch the relevant reports
    $reportQuery = SystemReport::where('indicator_id', $this->indicator_id)
        ->where('project_id', $this->project_id)
        ->where('financial_year_id', $this->financial_year['id'])
        ->where('crop', $this->crop);

    if ($this->organisation['id'] != 0) {
        $reportQuery->where('organisation_id', $this->organisation['id']);
    }

    $reportIds = $reportQuery->pluck('id');

    if ($reportIds->isEmpty()) {
        $this->data = [];
        $this->total = 0;
        return;
    }

    // 2. Fetch the data stored by your PopulatePreviousValue helper
    $reportData = SystemReportData::whereIn('system_report_id', $reportIds)->get();
    $groupedData = $reportData->groupBy('name');

    $disaggregations = IndicatorDisaggregation::where('indicator_id', $this->indicator_id)
        ->pluck('name')
        ->unique();

    // 3. Map the data: Just get the value, no math needed for the percentage anymore
    $this->data = $disaggregations->mapWithKeys(function ($name) use ($groupedData) {
        if (!$groupedData->has($name)) return [$name => 0];

        // If it's the percentage, we take the first/only value available
        // (since your helper saves it to the 'UNSPECIFIED' period report)
        if ($name === 'Total (% Percentage)') {
            return [$name => $groupedData[$name]->first()->value ?? 0];
        }

        // Keep sum() for non-percentage fields (like Volume or Hectares)
        return [$name => $groupedData[$name]->sum('value')];
    })->toArray();

    // 4. OVERWRITE GLOBAL TOTAL
    // If Global (ID 0), we pull the aggregate (weighted) growth from our tracker table
    if ($this->organisation['id'] == 0) {
        $calculatedRecord = \App\Models\PercentageIncreaseIndicator::where([
            'indicator_id'      => $this->indicator_id,
            'financial_year_id' => $this->financial_year['id'],
            'organisation_id'   => null,

        ])->first();

        if ($calculatedRecord) {
            $this->data['Total (% Percentage)'] = $calculatedRecord->growth_percentage;
        }
    }

    // 5. Final assignment
    $this->total = $this->data['Total (% Percentage)'] ?? $this->data['Total'] ?? 0;
}

    public function mount()
    {


        $this->calculations();
    }
}
