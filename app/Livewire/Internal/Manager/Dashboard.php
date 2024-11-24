<?php

namespace App\Livewire\Internal\Manager;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SystemReportData;
use Livewire\Attributes\Validate;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Dashboard extends Component
{
    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;
    public $attendance;
    public $showContent = false;
    public $quickForms;
    public $overviewIndicator;
    public function mount()
    {
        // Initialization code if required
    }

    public function loadData()
    {
        $this->loadIndicatorData();
        $this->loadSubmissionData();
        $this->loadAttendanceData();
        $this->loadQuickFormsData();
        $this->showContent = true;
    }

    private function loadIndicatorData()
    {

        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->overviewIndicator = $indicator;
        $organisation = auth()->user()->organisation;
        $currentDate = Carbon::now();

        $financialYear = FinancialYear::whereDate('start_date', '<=', $currentDate)
            ->whereDate('end_date', '>=', $currentDate)
            ->where('project_id', 1)
            ->first();

        if ($financialYear && $indicator) {
            $reportIds = SystemReport::where('indicator_id', $indicator->id)
                ->where('project_id', 1)
                ->where('financial_year_id', $financialYear->id)
                ->pluck('id');

            if ($reportIds->isNotEmpty()) {
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
                    'name' => '',
                ];
            }
        }
    }

    private function loadSubmissionData()
    {
        $this->submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('year', 'month')
            ->get()
            ->toArray();
    }

    private function loadAttendanceData()
    {
        $this->attendance = AttendanceRegister::take(5)->get();
    }

    private function loadQuickFormsData()
    {
        $this->quickForms = Form::with([
            'project',
            'indicators'
        ])
            ->where('name', '!=', 'REPORT FORM')
            ->take(5)
            ->get();
    }



    public function render()
    {
        return view('livewire.internal.manager.dashboard',[
            'projects' => Project::all()
            ]);
    }
}
