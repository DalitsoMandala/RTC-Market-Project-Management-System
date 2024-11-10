<?php

namespace App\Livewire\External;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SystemReportData;
use App\Models\ResponsiblePerson;
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
    public $pending;
    public $today;
    public $openSubmissions = 0;

    public $overviewIndicator;

    public function mount()
    {
        $user = User::find(auth()->id());
        $organisationId = $user->organisation->id;

        // Fetch indicator IDs associated with the user's organisation
        $myIndicators = ResponsiblePerson::where('organisation_id', $organisationId)
            ->whereHas('sources')  // Ensure 'sources' relationship exists
            ->pluck('indicator_id')
            ->toArray();

        // Query for open submission periods
        $this->openSubmissions = SubmissionPeriod::with([
            'form',
            'form.indicators'
        ])
            ->whereIn('indicator_id', $myIndicators)
            ->where('is_open', true)
            ->count();
    }

    public function loadData()
    {

        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->overviewIndicator = $indicator;
        $organisation = auth()->user()->organisation;
        $currentDate = Carbon::now();

        // Find current financial year record
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

            // Group and sum data by 'name' if reports are found
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
                    'name' => ''
                ];
            }
        }

        // Get submissions data grouped by year and month
        $this->submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('user_id', auth()->id())
            ->groupBy('year', 'month')
            ->get()
            ->toArray();

        // Count pending submissions and today's submissions for the user
        $this->pending = Submission::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count();

        $this->today = Submission::where('user_id', auth()->id())
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Get latest 5 attendance records
        $this->attendance = AttendanceRegister::latest()->take(5)->get();

        // Get latest 5 quick forms, excluding 'REPORT FORM'
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
        return view('livewire.external.dashboard', [
            'projects' => Project::all(),
        ]);
    }
}
