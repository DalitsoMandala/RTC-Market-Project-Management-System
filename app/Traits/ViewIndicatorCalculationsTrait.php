<?php

namespace App\Traits;

use App\Models\Indicator;
use App\Models\SystemReport;
use App\Models\SystemReportData;
use Illuminate\Support\Collection;
use App\Models\IndicatorDisaggregation;

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
        // Build base query for reports
        $reportQuery = SystemReport::where('indicator_id', $this->indicator_id)
            ->where('project_id', $this->project_id)
            ->where('financial_year_id', $this->financial_year['id'])
            ->where('crop', $this->crop);

        // Add organisation filter only if not global (id != 0)
        if ($this->organisation['id'] != 0) {
            $reportQuery->where('organisation_id', $this->organisation['id']);
        }

        $reportIds = $reportQuery->pluck('id');

        if ($reportIds->isEmpty()) {
            $this->data = [];
            $this->total = 0;
            return;
        }

        // Fetch and group data by 'name'
        $reportData = SystemReportData::whereIn('system_report_id', $reportIds)->get();
        $groupedData = $reportData->groupBy('name');

        // Get disaggregation keys with default value 0
        $disaggregations = IndicatorDisaggregation::where('indicator_id', $this->indicator_id)->pluck('name')->unique();
        $data = $disaggregations->mapWithKeys(fn($name) => [$name => $groupedData->has($name) ? $groupedData[$name]->sum('value') : 0]);

        // Assign results
        $this->data = $data->toArray();


        if ($groupedData->has('Total (% Percentage)')) {
            $this->total = $groupedData->get('Total (% Percentage)', 0);
        } elseif ($groupedData->has('Total')) {
            $this->total = $groupedData->get('Total', 0);
            }else{
                $this->total = 0;
        }
    }

    public function mount()
    {


        $this->calculations();
    }
}