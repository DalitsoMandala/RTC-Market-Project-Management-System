<?php

namespace App\Traits;

use App\Models\SystemReport;
use App\Models\SystemReportData;

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








    public function calculations()
    {




        $reportId = SystemReport::where('indicator_id', $this->indicator_id)
            ->where('project_id', $this->project_id)
            ->where('organisation_id', $this->organisation['id'])
            ->where('financial_year_id', $this->financial_year['id'])
            ->pluck('id');



        if ($this->organisation['id'] == 0) {
            $reportId = SystemReport::where('indicator_id', $this->indicator_id)
                ->where('project_id', $this->project_id)
                ->where('financial_year_id', $this->financial_year['id'])
                ->pluck('id');
        }

        if ($reportId->isNotEmpty()) {
            // Retrieve and group data by 'name'
            $data = SystemReportData::whereIn('system_report_id', $reportId)->get();
            $groupedData = $data->groupBy('name');


            // Sum each group's values

            $summedGroups = $groupedData->map(function ($group) {
                return $group->sum('value'); // Changed from first()->value to sum('value')
            });



            // Store the results
            $this->data = $summedGroups;
            if ($summedGroups->has('Total (% Percentage)')) {
                $this->total = $summedGroups->get('Total (% Percentage)', 0);
            } elseif ($summedGroups->has('Total')) {
                $this->total = $summedGroups->get('Total', 0);
            }else{
                $this->total = 0;
            }
        }
    }
    public function mount()
    {

        $this->calculations();
    }
}
