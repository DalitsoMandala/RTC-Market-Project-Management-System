<?php

namespace App\Livewire\Internal\Staff;

use App\Models\Form;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SystemReportData;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;


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
        // Initialization if needed
    }

    public function loadData()
    {

        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $organisation = auth()->user()->organisation;
        $currentDate = now();

        // Retrieve the current financial year record
        $record = FinancialYear::whereDate('start_date', '<=', $currentDate)
            ->whereDate('end_date', '>=', $currentDate)
            ->where('project_id', 1)
            ->first();

        if ($record && $indicator) {
            // Get report IDs related to the indicator and financial year
            $reportIds = SystemReport::where('indicator_id', $indicator->id)
                ->where('project_id', 1)
                ->where('financial_year_id', $record->id)
                ->pluck('id');

            if ($reportIds->isNotEmpty()) {
                // Retrieve and group data by 'name' and sum each group's values
                $data = SystemReportData::whereIn('system_report_id', $reportIds)->get();
                $groupedData = $data->groupBy('name');
                $summedGroups = $groupedData->map(fn($group) => $group->sum('value'));

                $this->data = [
                    'actors' => $summedGroups->toArray(),
                    'name' => $indicator->indicator_name,
                ];
                $this->indicatorCount = Indicator::count();
            } else {
                $this->data = [
                    'actors' => [],
                    'name' => ''
                ];
            }
        }

        // Fetch submission data grouped by year and month
        $this->submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        // Get the latest 5 attendance records
        $this->attendance = AttendanceRegister::latest()->take(5)->get();

        // Get the latest 5 quick forms, excluding 'REPORT FORM'
        $this->quickForms = Form::with([
            'project',
            'indicators'
        ])
            ->where('name', '!=', 'REPORT FORM')
            ->latest()
            ->take(5)
            ->get();

        $this->showContent = true;
    }

    public function render()
    {
        return view('livewire.internal.staff.dashboard', [
            'projects' => Project::all(),
        ]);
    }
}
