<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
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
 public $project;
    public $indicatorCount;
    public $submissions;

    public $attendance;

    public $quickForms;

    public $submissionCategories;
    public $lastSubmission = [];
    public $users = [];
public $indicatorData = [];
    public $topData = [
        'users' => null,
        'inactiveUsers' => null,
        'activeUsers' => null,
        'projects' => null,
        'forms' => null,
        'indicators' => null

    ];
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
               $this->topData['users'] = User::count();
        $this->topData['inactiveUsers'] = User::withTrashed()->whereNotNull('deleted_at')->count();
        $this->topData['activeUsers'] = User::withTrashed()->whereNull('deleted_at')->count();
        $this->topData['projects'] = Project::count();
        $this->topData['forms'] = Form::count();
        $this->topData['indicators'] = Indicator::count();

        $this->loadData();
    }

    public function loadData($selectedReportYear = null)
    {
        $records = FinancialYear::where('project_id', 1)->get(); // retrieve all records from database
        $financialYear = FinancialYear::where('status','active')->first()->number ?? 2;




        if ($selectedReportYear) {


            $this->data = $this->calculations($selectedReportYear);


            $this->dispatch(
                'updateChartData',
                data: $this->data,


            );

            return;
        }


        $this->data = $this->calculations($financialYear);

        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->name = $indicator->indicator_name;
        $this->financialYears = $records->toArray();
        $this->selectedReportYear = $financialYear;


        $this->loadSubmissionData();
        $this->loadAttendanceData();
        $this->loadQuickFormsData();
        $this->loadUsers();

        $this->loadLastSubmissions();
        $this->loadLastSubmissions();
    }

    public function calculations($financialYear = 2)
    {
        // Fetch matching report IDs
        $reportIds = SystemReport::where('indicator_id', 1)
            ->where('project_id', 1)
            ->where('financial_year_id', $financialYear)
            ->where('crop', null)
            ->pluck('id');

        // Return summed data grouped by 'name' if reports exist
        if ($reportIds->isNotEmpty()) {
            $data = SystemReportData::whereIn('system_report_id', $reportIds)->get();

            return $data->groupBy('name')->map(function ($group) {
                return $group->sum('value');
            });
        }

        return collect(); // Return an empty collection instead of null for consistency
    }

    private function loadIndicatorData()
    {


        $indicator = Indicator::where('indicator_no', 'A1')->first();
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

                $this->indicatorData = [
                    'actors' => $summedGroups->toArray(),
                    'name' => $indicator->indicator_name,
                ];
                $this->indicatorCount = Indicator::count();
            } else {
                $this->indicatorData = [
                    'actors' => [],
                    'name' => '',
                ];
            }
        }
    }

    private function loadSubmissionData()
    {
        $this->submissions = Submission::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total, batch_type as type')
            ->groupBy('year', 'month', 'batch_type')
            ->get()
            ->toArray();

    }

    private function loadSubmissions()
    {
        $this->submissionCategories = Submission::select([
            DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
            DB::raw('SUM(CASE WHEN status = "denied" THEN 1 ELSE 0 END) as denied'),
        ])->first()->toArray();
    }

    private function  loadLastSubmissions()
    {

        $this->lastSubmission = Submission::query()->with(['period.indicator', 'user.organisation', 'user',  'period.reportingMonths', 'form', 'period.financialYears'])->take(5)->orderBy('created_at', 'desc')->get();
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
            ->whereNot('name', 'REPORT FORM')
            ->take(5)
            ->get();
    }

    private function loadUsers()
    {
        $this->users = User::with('roles')->inRandomOrder()->take(5)->get();
    }
    public function render()
    {
        return view('livewire.dashboard-charts');
    }
}
