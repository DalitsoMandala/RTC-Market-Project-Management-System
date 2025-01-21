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
        $financialYear = 1;
        foreach ($records as $record) {
            $startDate = Carbon::parse($record->start_date);
            $endDate = Carbon::parse($record->end_date);

            if (Carbon::now()->between($startDate, $endDate)) {
                // current date falls between start and end dates for this record
                // do something with this record

                $financialYear = $record->id;
                break;
            }
        }

        $systemReport = SystemReport::with('data')->where('financial_year_id', $financialYear)
            ->where('indicator_id', 1)->where('project_id', 1)
            ->pluck('id');

        if ($selectedReportYear) {


            $systemReport = SystemReport::with('data')->where('financial_year_id', $selectedReportYear)
                ->where('indicator_id', 1)->where('project_id', 1)
                ->pluck('id');

            $data = SystemReportData::whereIn('system_report_id', $systemReport)->get();
            $groupedData = $data->groupBy('name');
            $summedGroups = $groupedData->map(function ($group) {
                return $group->sum('value'); // Assuming 'value' is the field to be summed
            });

            $this->data = $summedGroups;


            $this->dispatch(
                'updateChartData',
                data: $this->data,


            );

            return;
        }


        $data = SystemReportData::whereIn('system_report_id', $systemReport)->get();
        $groupedData = $data->groupBy('name');
        $summedGroups = $groupedData->map(function ($group) {
            return $group->sum('value'); // Assuming 'value' is the field to be summed
        });

        $this->data = $summedGroups;
        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->name = $indicator->indicator_name;
        $this->financialYears = $records->toArray();
        $this->selectedReportYear = FinancialYear::find($financialYear)->number;
    }


    public function render()
    {
        return view('livewire.dashboard-charts');
    }
}
