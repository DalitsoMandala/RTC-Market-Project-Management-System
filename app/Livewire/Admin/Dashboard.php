<?php

namespace App\Livewire\Admin;

use App\Models\Form;
use App\Models\User;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Project;

class Dashboard extends Component
{

    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;

    public $attendance;
    public $showContent = false;
    public $quickForms;

    public $submissionCategories;
    public $users = [];

    public $topData = [
        'users' => null,
        'inactiveUsers' => null,
        'activeUsers' => null,
        'projects' => null,
        'forms' => null,
        'indicators' => null

    ];
    public function mount()
    {


        $this->topData['users'] = User::count();
        $this->topData['inactiveUsers'] = User::withTrashed()->whereNotNull('deleted_at')->count();
        $this->topData['activeUsers'] = User::withTrashed()->whereNull('deleted_at')->count();
        $this->topData['projects'] = Project::count();
        $this->topData['forms'] = Form::count();
        $this->topData['indicators'] = Indicator::count();

    }

    public function loadData()
    {
        $this->loadIndicatorData();
        $this->loadSubmissionData();
        $this->loadAttendanceData();
        $this->loadQuickFormsData();
        $this->loadUsers();
        $this->loadSubmissions();
        $this->showContent = true;
    }

    private function loadIndicatorData()
    {

        $indicator = Indicator::where('indicator_no', 'A1')->first();

        if ($indicator) {
            $this->fill([
                'indicatorCount' => Indicator::count(),
            ]);

            $this->data = [
                'actors' => $indicatorA1->getDisaggregations(),
                'name' => $indicator->indicator_name,
            ];
        } else {
            // Handle the case where the indicator is not found
            $this->data = [
                'actors' => [],
                'name' => '',
            ];
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

    public function loadSubmissions()
    {
        $this->submissionCategories = Submission::select([
            DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
            DB::raw('SUM(CASE WHEN status = "denied" THEN 1 ELSE 0 END) as denied'),
        ])->first()->toArray();


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

    public function loadUsers()
    {
        $this->users = User::with('roles')->inRandomOrder()->take(5)->get();
    }


    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
