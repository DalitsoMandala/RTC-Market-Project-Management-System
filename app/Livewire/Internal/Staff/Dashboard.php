<?php

namespace App\Livewire\Internal\Staff;

use App\Models\Form;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SystemReport;
use Livewire\Attributes\Lazy;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;
use App\Helpers\rtc_market\indicators\A1;
use App\Helpers\rtc_market\indicators\indicator_A1;

class Dashboard extends Component
{

    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;

    public $attendance;
    public $showContent = false;
    public $quickForms;
    public function mount()
    {


    }

    public function loadData()
    {
        $indicatorA1 = new Indicator_A1();
        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $organisation = auth()->user()->organisation;
        $currentDate = Carbon::now();
        $record = FinancialYear::
            whereDate('start_date', '<=', $currentDate)  // Current date is on or after start_date
            ->whereDate('end_date', '>=', $currentDate)    // Current date is on or before end_date
            ->where('project_id', 1)

            ->first();

        $reportId = SystemReport::where('indicator_id', $indicator->id)
            ->where('project_id', 1)
            //    ->where('organisation_id', $organisation->id)
            ->where('financial_year_id', $record->id)
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

        }



        if ($indicator && $this->data->isNotEmpty()) {
            $this->fill([
                'indicatorCount' => Indicator::count(),
            ]);

            $this->data = [
                'actors' => $this->data->toArray(),
                'name' => $indicator->indicator_name,
            ];
        } else {
            // Handle the case where the indicator is not found
            $this->data = [
                'actors' => [],
                'name' => '',
            ];
        }

        $this->submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('year', 'month')
            ->get()->toArray();

        $this->attendance = AttendanceRegister::get()->take(5);
        $this->quickForms = Form::with([
            'project',
            'indicators'
        ])->whereNot('name', 'REPORT FORM')->get()->take(5);


        $this->showContent = true;
    }



    public function render()
    {
        return view('livewire.internal.staff.dashboard', [
            'projects' => Project::get(),
        ]);
    }
}
