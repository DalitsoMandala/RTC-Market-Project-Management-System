<?php

namespace App\Livewire\Indicators\RtcMarket;

use App\Models\FinancialYear;
use App\Models\SystemReport;
use App\Models\SystemReportData;

use Livewire\Component;

use App\Models\Indicator;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Helpers\rtc_market\indicators\A1;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Helpers\rtc_market\indicators\indicator_A1;

class IndicatorA1 extends Component
{
    use LivewireAlert;
    #[Validate('required')]
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



    public function setData($id)
    {
        $this->resetErrorBag();

    }



    public function calculations()
    {



        $reportId = SystemReport::where('indicator_id', $this->indicator_id)
            ->where('project_id', $this->project_id)
            ->where('organisation_id', $this->organisation['id'])
            ->where('financial_year_id', $this->financial_year['id'])
            ->pluck('id');

        if ($reportId->isNotEmpty()) {
            // Retrieve and group data by 'name'
            $data = SystemReportData::whereIn('system_report_id', $reportId)->get();
            $groupedData = $data->groupBy('name');

            // Sum each group's values
            $summedGroups = $groupedData->map(function ($group) {
                return $group->sum('value'); // Assuming 'value' is the field to be summed
            });


            // Store the results
            $this->data = $summedGroups;

            // Retrieve the total if 'Total' is one of the grouped items
            $this->total = $summedGroups->get('Total', 0); // Defaults to 0 if 'Total' is not present
        }




    }


    public function mount()
    {

        $this->calculations();

    }
    public function render()
    {
        return view('livewire.indicators.rtc-market.indicator-a1');
    }
}
