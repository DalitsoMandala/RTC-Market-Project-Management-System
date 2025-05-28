<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SystemReportData;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DashboardCharts extends Component
{
    use LivewireAlert;
    public $data = [];
    public $showContent = false;
    public $name = null;
    public $financialYears = [];
    public $selectedReportYear = 1;

    #[On('showCharts')]
    public function showVisuals()
    {

        $this->showContent = true;
    }
    #[On('updateReportYear')]
    public function updateReportYear($id)
    {
        $this->showContent = false;
        $this->selectedReportYear = FinancialYear::find($id)->number;
        $this->loadData($id);
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData($selectedReportYear = null)
    {
        $records = FinancialYear::where('project_id', 1)->get(); // retrieve all records from database
        $financialYear = 2;




        if ($selectedReportYear) {


            $this->data = $this->calculations($selectedReportYear);


            $this->dispatch(
                'updateChartData',
                data: $this->data,


            );

            return;
        }


        $this->data = $this->calculations();

        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->name = $indicator->indicator_name;
        $this->financialYears = $records->toArray();
        $this->selectedReportYear = FinancialYear::find($financialYear)->number;
    }

    public function calculations($financialYear = null)
    {






        $reportId = SystemReport::where('indicator_id', 1)
            ->where('project_id', 1)
            ->where('financial_year_id', 2)
            ->pluck('id');

        if ($financialYear) {
            $reportId = SystemReport::where('indicator_id', 1)
                ->where('project_id', 1)
                ->where('financial_year_id', $financialYear)
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
            return $summedGroups;
        }
    }
    public function render()
    {
        return view('livewire.dashboard-charts');
    }
}
